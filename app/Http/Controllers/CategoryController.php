<?php

namespace App\Http\Controllers;

use Exception;
use App\Category;
use App\Helpers\Cleaner;
use App\Helpers\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $data['title']= "Category Lists";
        $data['breadcrumbs'] = [
            ['label' => 'Master Data'],
            ['label' => 'Category'],
        ];

        $data['categories'] = Category::with(['creator', 'updater'])->get();
        
        return view('contents.master.category.index', $data);
    }

    public function store(Request $request)
    {
        $username = Auth::user()->username;

        $validated = $request->validate([
            'name' => 'required|unique:categories',
        ]);

        $cleaned = Cleaner::cleanAll($validated);

        $data = [
            'name' => strtolower($cleaned['name']),
            'created_by' => $username,
            'updated_by' => $username,
        ];

        try {
            DB::beginTransaction();
            $category = Category::create($data);

            LogActivity::log('add-category', 'Successfully added category: ' . $category->name, '', $username);
            DB::commit();
            return redirect()->back()->with('success', 'Category successfully added!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error adding category: ' . $e->getMessage());
            LogActivity::log('add-category', 'Failed to add category: ' . ($category->name ?? $cleaned['name']), $e->getMessage(), $username);
            return redirect()->back()->with('error', 'Failed to add category!');
        }
    }

    public function update(Request $request)
    {
        $id = decrypt($request->input('id'));
        $category = Category::findOrFail($id);
        $username = Auth::user()->username;

        $rules = [
            'edit_name' => 'required|unique:categories,name,' . $id,
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            LogActivity::log(
                'edit-category',
                'Validation failed while editing category: ' . $category->name,
                json_encode($validator->errors()->all()),
                $username
            );
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $cleaned = Cleaner::cleanAll($request->only(['edit_name']));
        $newData = [
            'name' => strtolower($cleaned['edit_name']),
            'updated_by' => $username,
            'updated_at' => now(),
        ];

        $changes = [];

        if ($category->name !== $newData['name']) {
            $changes[] = "Name changed from '{$category->name}' to '{$newData['name']}'";
        }

        $logDetail = $changes ? implode(PHP_EOL, $changes) : 'No changes detected.';

        try {
            DB::beginTransaction();

            $category->update($newData);

            LogActivity::log('edit-category', "Successfully edited category: {$category->name}", $logDetail, $username);

            DB::commit();

            return redirect()->back()->with('success', 'Category successfully edited!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error editing category: ' . $e->getMessage());
            LogActivity::log('edit-category', "Failed to edit category: {$category->name}", $e->getMessage(), $username);

            return redirect()->back()->with('error', 'Failed to edit category!');
        }
    }

    public function destroy($id)
    {
        $id = decrypt($id);
        $category = Category::findOrFail($id);
        $username = Auth::user()->username;

        $shipmentCount = DB::table('shipments')
            ->where('category_id', $id)
            ->count();

        if ($shipmentCount > 0) {
            LogActivity::log('delete-category', 'Failed to delete category: ' . $category->name, 'Category is being used by another shipment.', $username);
            return redirect()->back()->with('error', 'Category is being used by another shipment.');
        }

        try {
            DB::beginTransaction();

            $category->delete();

            LogActivity::log('delete-category', 'Successfully deleted category: ' . $category->name, '', $username);
            DB::commit();
            return redirect()->back()->with('success', 'Category successfully deleted!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting category: ' . $e->getMessage());
            LogActivity::log('delete-category', 'Failed to delete category: ' . $category->name, $e->getMessage(), $username);
            return redirect()->back()->with('error', 'Failed to delete category!');
        }
    }

    // Ajax Function
    public function checkUniqueName(Request $request)
    {
        $name = strtolower($request->query('name'));
        $id = decrypt($request->query('id'));
        $exists = category::where('name', $name)->where('id', '<>', $id)->exists();
        return response()->json(['unique' => !$exists]);
    }
}