<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class CategoryController extends Controller
{
    /**
     * @var string Paths
     */
    protected $viewPath = 'admin.categories';

    protected $imagePath = 'categories';

    /**
     * @var Category
     */
    protected $category;

    /**
     * CategoryController constructor.
     * @param Category $category
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|Response
     */
    public function index()
    {
        if (!hwa_check_permission('view_category')) {
            abort(404);
        }

        $path = $this->viewPath;
        $categories = $this->category->whereNull('parent_id')
            ->with(['childCategories'])
            ->select(['id', 'name', 'images', 'active', 'parent_id'])
            ->get();
        return view("{$path}.index")->with([
            'path' => $path,
            'results' => $categories ?? []
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View|Response
     */
    public function create()
    {
        if (!hwa_check_permission('add_category')) {
            abort(404);
        }

        $path = $this->viewPath;
        $categories = $this->category->whereNull('parent_id')
            ->with(['childCategories'])
            ->select(['id', 'name', 'parent_id'])
            ->get();
        return view("{$path}.form")->with([
            'path' => $path,
            'categories' => $categories ?? []
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        if (!hwa_check_permission('add_category')) {
            abort(404);
        }

        $path = $this->viewPath;
        // Validate rule
        $validator = $this->validateData($request);
        if ($validator->fails()) {
            // Invalid data
            hwa_notify_error($validator->getMessageBag()->first());
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            if ($this->updateOrCreate($request)) {
                // Add success
                hwa_notify_success("Thêm danh mục thành công.");
                return redirect()->route("{$path}.index");
            } else {
                hwa_notify_error("Lỗi thêm danh mục.");
                return redirect()->back()->withInput();
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return Application|Factory|View|Response
     */
    public function edit($id)
    {
        $path = $this->viewPath;
        if (!hwa_check_permission('edit_category') || !$result = $this->category->select(['id', 'name', 'description', 'images', 'active', 'parent_id'])->find($id)) {
            abort(404);
        } else {
            $categories = $this->category->whereNull('parent_id')
                ->whereNotIn('id', [$id])
                ->with(['childCategories'])
                ->select(['id', 'name', 'parent_id'])
                ->get();
            return view("{$path}.form")->with([
                'path' => $path,
                'result' => $result ?? [],
                'categories' => $categories ?? []
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $path = $this->viewPath;
        if (!hwa_check_permission('edit_category') || !$result = $this->category->select(['id', 'name', 'description', 'images', 'active', 'parent_id'])->find($id)) {
            abort(404);
        } else {
            // Validate rule
            $validator = $this->validateData($request);
            if ($validator->fails()) {
                // Invalid data
                hwa_notify_error($validator->getMessageBag()->first());
                return redirect()->back()->withInput()->withErrors($validator);
            } else {
                if ($this->updateOrCreate($request, $result)) {
                    // Add success
                    hwa_notify_success("Cập nhật danh mục thành công.");
                    return redirect()->route("{$path}.index");
                } else {
                    hwa_notify_error("Lỗi cập nhật danh mục.");
                    return redirect()->back()->withInput();
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        if (!hwa_check_permission('delete_category') || !$result = $this->category->find($id)) {
            abort(404);
        } else {
            if ($result->delete()) {
                // delete success
                if (file_exists($path = hwa_image_path($this->imagePath, $result['images']))) {
                    File::delete($path);
                }
                hwa_notify_success("Xóa danh mục thành công.");
            } else {
                hwa_notify_error("Lỗi xóa danh mục.");
            }
            return redirect()->back();
        }
    }

    /**
     * Validate data
     *
     * @param $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validateData($request)
    {
        return Validator::make($request->all(), [
            'name' => ['required', 'max:191'],
            'parent_id' => ['nullable', Rule::in(array_values($this->category->whereNull('parent_id')->get()->pluck('id')->toArray()))],
            'active' => ['required', Rule::in(['0', '1'])]
        ], [
            'name.required' => 'Tên danh mục là trường bắt buộc.',
            'name.max' => 'Tên danh mục có tối đa 191 ký tự.',
            'parent_id.in' => 'Danh mục cha không hợp lệ.',
            'active.required' => 'Trạng thái là trường bắt buộc.',
            'active.in' => 'Trạng thái không hợp lệ.',
        ]);
    }

    /**
     * Save data
     *
     * @param $request
     * @param null $category
     * @return bool
     */
    protected function updateOrCreate($request, $category = null)
    {
        if ($request->has('image')) {
            $file = $request->file('image');
            $image = strtolower("hwa_" . md5(Str::random(16) . time() . Str::random(18)) . '.' . $file->getClientOriginalExtension());
            Image::make($file->getRealPath())->save(hwa_image_path($this->imagePath, $image));
        } else {
            $image = $category['images'] ?? '';
        }

        // Get data
        $data = [
            'name' => $request['name'],
            'description' => $request['description'],
            'parent_id' => $request['parent_id'],
            'images' => $image,
            'active' => $request['active'],
        ];

        if (!$category) {
            // Create new
            if (!$this->category->create($data)) {
                if (file_exists($path = hwa_image_path($this->imagePath, $image))) {
                    File::delete($path);
                }
                return false;
            }
            return true;
        } else {
            // Update
            $old_image = $category['images'];
            if ($category->fill($data)->save()) {
                if ($request->has('image')) {
                    if (file_exists($path = hwa_image_path($this->imagePath, $old_image))) {
                        File::delete($path);
                    }
                }
                return true;
            } else {
                if ($request->hasFile('image')) {
                    if (file_exists($new_path = hwa_image_path($this->imagePath, $image))) {
                        File::delete($new_path);
                    }
                }
                return false;
            }
        }
    }
}
