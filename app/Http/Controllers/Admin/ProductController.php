<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Carbon\Carbon;
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

class ProductController extends Controller
{
    /**
     * @var string Paths
     */
    protected $viewPath = 'admin.products';

    protected $imagePath = 'products';

    /**
     * @var Category
     */
    protected $category;

    /**
     * @var Supplier
     */
    protected $supplier;

    /**
     * @var Product
     */
    protected $product;

    /**
     * ClientProductController constructor.
     * @param Product $product
     * @param Category $category
     * @param Supplier $supplier
     */
    public function __construct(Product $product, Category $category, Supplier $supplier)
    {
        $this->category = $category;
        $this->product = $product;
        $this->supplier = $supplier;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|Response
     */
    public function index()
    {
        if (!hwa_check_permission('view_product')) {
            abort(404);
        }

        $path = $this->viewPath;
        $attr = $this->product->getFillable();
        $attr = array_merge($attr, ['id']);

        $results = $this->product->with([
            'category',
        ])->select($attr)->get();

        return view("{$path}.index")->with([
            'path' => $path,
            'results' => $results ?? []
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        if (!hwa_check_permission('add_product')) {
            abort(404);
        }

        $path = $this->viewPath;
        return view("{$path}.form")->with([
            'path' => $path,
            'categories' => $this->category->whereNull('parent_id')->with(['childCategories'])->whereActive(1)->select(['id', 'name', 'parent_id'])->get(),
            'suppliers' => $this->supplier->whereActive(1)->select(['id', 'name'])->get(),
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
        if (!hwa_check_permission('add_product')) {
            abort(404);
        }

        $path = $this->viewPath;
        // Validate rule
        $validator = $this->validateData($request);
        if ($validator->fails()) {
            hwa_notify_error($validator->getMessageBag()->first());
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            if ($this->updateOrCreate($request)) {
                // Add success
                hwa_notify_success("Thêm sản phẩm thành công.");
                return redirect()->route("{$path}.index");
            } else {
                hwa_notify_error("Lỗi thêm sản phẩm.");
                return redirect()->back()->withInput();
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $id
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
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        if (!hwa_check_permission('edit_product') || !$result = $this->product->find($id)) {
            abort(404);
        } else {
            $path = $this->viewPath;
            return view("{$path}.form")->with([
                'path' => $path,
                'result' => $result,
                'categories' => $this->category->whereNull('parent_id')->with(['childCategories'])->whereActive(1)->select(['id', 'name', 'parent_id'])->get(),
                'suppliers' => $this->supplier->whereActive(1)->select(['id', 'name'])->get(),
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $path = $this->viewPath;
        if (!hwa_check_permission('edit_product') || !$result = $this->product->find($id)) {
            abort(404);
        } else {
            // Validate rule
            $validator = $this->validateData($request, $id);
            if ($validator->fails()) {
                // Invalid data
                hwa_notify_error($validator->getMessageBag()->first());
                return redirect()->back()->withInput()->withErrors($validator);
            } else {
                if ($this->updateOrCreate($request, $result)) {
                    // Add success
                    hwa_notify_success("Cập nhật sản phẩm thành công.");
                    return redirect()->route("{$path}.index");
                } else {
                    hwa_notify_error("Lỗi cập nhật sản phẩm.");
                    return redirect()->back()->withInput();
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param   $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        if (!hwa_check_permission('delete_product') || !$result = $this->product->find($id)) {
            abort(404);
        } else {
            $images = $result->images;
            if ($result->delete()) {
                foreach (explode(',', $images) as $image) {
                    if (file_exists($pathImage = hwa_image_path($this->imagePath, $image))) {
                        File::delete($pathImage);
                    }
                }
                hwa_notify_success("Xóa sản phẩm thành công.");
            } else {
                hwa_notify_error("Lỗi xóa sản phẩm.");
            }
            return redirect()->back();
        }
    }

    /**
     * Validate data
     *
     * @param $request
     * @param null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validateData($request, $id = null)
    {
        $rules = [
            'sku' => ['required', 'max:191', 'min:6', 'unique:products,sku'],
            'name' => ['required', 'max:191'],
            'price' => ['required', 'min:0', 'not_in:0'],
            'description' => ['required', 'max:255'],
            'content' => ['required'],
            'quantity' => ['required', 'min:0', 'not_in:0'],
            'unit_price' => ['required', 'min:0', 'not_in:0'],
            'category_id' => ['required', Rule::in(array_merge($this->category->select(['id'])->get()->pluck('id')->toArray()))],
            'supplier_id' => ['required', Rule::in(array_merge($this->supplier->select(['id'])->get()->pluck('id')->toArray()))],
            'seo_title' => ['nullable', 'max:191'],
            'seo_description' => ['nullable', 'max:255'],
            'thumb' => ['required', 'image', 'mimes:jpeg,png,jpg'],
            'images' => ['required'],
            'active' => ['required', Rule::in(['draft', 'published', 'unpublished'])]
        ];

        $messages = [
            'sku.required' => 'Mã sản phẩm là trường bắt buộc.',
            'sku.max' => 'Mã sản phẩm có tối đa 191 ký tự.',
            'sku.min' => 'Mã sản phẩm có tối thiểu 191 ký tự.',
            'sku.unique' => 'Mã sản phẩm đã tồn tại.',
            'name.required' => 'Tên sản phẩm là trường bắt buộc.',
            'name.max' => 'Tên sản phẩm có tối đa 191 ký tự.',
            'price.required' => 'Giá sản phẩm là trường bắt buộc.',
            'price.min' => 'Giá sản phẩm phải lớn hơn 0.',
            'price.not_in' => 'Giá sản phẩm phải lớn hơn 0.',
            'description.required' => 'Mô tả sản phẩm là trường bắt buộc.',
            'description.max' => 'Mô tả sản phẩm có tối đa 191 ký tự.',
            'content.required' => 'Chi tiết sản phẩm là trường bắt buộc.',
            'quantity.required' => 'Số lượng là trường bắt buộc.',
            'quantity.not_in' => 'Số lượng phải lớn hơn 0.',
            'unit_price.required' => 'Đơn giá là trường bắt buộc.',
            'unit_price.not_in' => 'Đơn giá phải lớn hơn 0.',
            'category_id.required' => 'Danh mục sản phẩm là trường bắt buộc.',
            'category_id.in' => 'Danh mục sản phẩm không hợp lệ.',
            'supplier_id.required' => 'Nhà cung cấp là trường bắt buộc.',
            'supplier_id.in' => 'Nhà cung cấp không hợp lệ.',
            'seo_title.max' => 'Tiêu đề SEO có tối đa 191 ký tự.',
            'seo_description.max' => 'Mô tả SEO có tối đa 255 ký tự.',
            'images.required' => 'Hình ảnh là trường bắt buộc.',
            'thumb.required' => 'Ảnh đại diện là trường bắt buộc.',
            'thumb.image' => 'Ảnh đại diện là không hợp lệ.',
            'thumb.mimes' => 'Định dạng ảnh không hợp lệ.',
            'active.required' => 'Trạng thái là trường bắt buộc.',
            'active.in' => 'Trạng thái không hợp lệ.',
        ];

        if (!empty($id)) {
            unset($rules['sku']);
            unset($rules['thumb']);
            unset($rules['images']);

            $rules = array_merge($rules, [
                'sku' => ['required', 'max:191', 'min:6', 'unique:products,sku,' . $id],
                'thumb' => ['nullable', 'image', 'mimes:jpeg,png,jpg'],
            ]);

            unset($messages['images.required']);
            unset($messages['thumb.required']);
        }

        return Validator::make($request->all(), $rules, $messages);
    }

    /**
     * Save data
     *
     * @param $request
     * @param null $product
     * @return bool
     */
    protected function updateOrCreate($request, $product = null)
    {
        // Get product data
        $data = [
            'sku' => strtoupper(trim($request['sku'])),
            'name' => $request['name'],
            'price' => $request['price'],
            'description' => $request['description'],
            'content' => $request['content'],
            'quantity' => $request['quantity'],
            'unit_price' => $request['unit_price'],
            'category_id' => $request['category_id'],
            'supplier_id' => $request['supplier_id'],
            'seo_title' => $request['seo_title'] ?? $request['name'],
            'seo_description' => $request['seo_description'] ?? $request['description'],
            'seo_keyword' => $request['seo_keyword'],
            'active' => $request['active'],
        ];

        if (!$product) {
            // Create new product

            // Get thumb image
            $thumb = '';
            if ($request->has('thumb')) {
                $file = $request->file('thumb');
                $thumb = strtolower("hwa_" . md5(Str::random(16) . time() . Str::random(18)) . '.' . $file->getClientOriginalExtension());
                Image::make($file->getRealPath())->resize(540, 600)->save(hwa_image_path($this->imagePath . '/thumbs', $thumb));
            }

            // Get product images
            $images = [];
            if ($files = $request->file('images')) {
                $i = 0;
                foreach ($files as $file) {
                    $imageName = strtolower("hwa_" . md5(time() . $i . Str::random(32)) . '.' . $file->getClientOriginalExtension());
                    Image::make($file->getRealPath())->resize(540, 600)->save(hwa_image_path($this->imagePath, $imageName));
                    $images[] = $imageName;
                    $i++;
                }
            }

            // Merge data with images
            $data = array_merge($data, [
                'thumb' => $thumb,
                'image' => implode(',', $images),
            ]);

            if (!$this->product->create($data)) {
                // Delete thumb if create fail
                if (file_exists($path = hwa_image_path($this->imagePath . '/thumbs', $thumb))) {
                    File::delete($path);
                }

                // Delete images if create fail
                foreach ($images as $image) {
                    if (file_exists($pathImages = hwa_image_path($this->imagePath, $image))) {
                        File::delete($pathImages);
                    }
                }
                return false;
            }
            return true;
        } else {
            // Update product

            $old_thumb = $product['thumb'] ?? '';
            if ($request->has('thumb')) {
                $file = $request->file('thumb');
                $thumb = strtolower("hwa_" . md5(Str::random(16) . time() . Str::random(18)) . '.' . $file->getClientOriginalExtension());
                Image::make($file->getRealPath())->resize(540, 600)->save(hwa_image_path($this->imagePath . '/thumbs', $thumb));
            } else {
                $thumb = $old_thumb;
            }

            $oldImages = explode(',', $product->image);

            $oldImagesInput = [];
            $request['olderImage'] = $request['olderImage'] ?? [];
            // get old image input
            foreach ($request['olderImage'] as $imageItem) {
                $oldImagesInput[] = isset($oldImages[$imageItem]) ? $oldImages[$imageItem] : null;
            }

            $images = [];
            if ($files = $request->file('images')) {
                // Get and move new images
                $i = 0;
                foreach ($files as $file) {
                    $imageName = strtolower("hwa_" . md5(time() . $i . Str::random(32)) . '.' . $file->getClientOriginalExtension());
                    Image::make($file->getRealPath())->resize(540, 600)->save(hwa_image_path($this->imagePath, $imageName));
                    $images[] = $imageName;
                    $i++;
                }
                $updateImages = implode(',', $images + $oldImagesInput);
            } else {
                // if don't change images
                $updateImages = implode(',', $oldImagesInput);
            }

            // Image delete list
            $deleteImg = array_diff($oldImages, $oldImagesInput);

            // Merge data with image
            $data = array_merge($data, [
                'thumb' => $thumb,
                'image' => $updateImages,
            ]);

            if ($product->fill($data)->save()) {
                if ($request->has('thumb')) {
                    // Delete thumb if create fail
                    if (file_exists($pathThumb = hwa_image_path($this->imagePath . '/thumbs', $old_thumb))) {
                        File::delete($pathThumb);
                    }
                }

                // Delete old images if update success
                foreach ($deleteImg as $item) {
                    if (file_exists($path = hwa_image_path($this->imagePath, $item))) {
                        File::delete($path);
                    }
                }
                return true;
            } else {
                // Delete thumb if create fail
                if (file_exists($pathThumb = hwa_image_path($this->imagePath . '/thumbs', $thumb))) {
                    File::delete($pathThumb);
                }

                // Delete old image if update fail
                foreach ($images as $image) {
                    if (file_exists($path = hwa_image_path($this->imagePath, $image))) {
                        File::delete($path);
                    }
                }
                return false;
            }
        }
    }

}
