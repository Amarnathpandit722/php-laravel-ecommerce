@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="Create Product">

        {{-- Back link --}}
        <div class="mb-6">
            <a href="{{ route('products.index') }}"
               class="inline-flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to products
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-6 px-4 py-3 bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 rounded-lg">
                <div class="flex items-start gap-2">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <ul class="list-disc list-inside text-sm space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data"
              id="productForm" class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            @csrf

            {{-- LEFT: main info --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Basic info --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <span class="w-1 h-5 bg-indigo-500 rounded"></span>
                        Basic Information
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Product Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name"
                                   value="{{ old('name') }}" required maxlength="255"
                                   placeholder="e.g. Wireless Bluetooth Headphones"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                            <p class="text-xs text-gray-400 mt-1">
                                Slug preview: <span id="slugPreview" class="font-mono text-indigo-500">—</span>
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Category <span class="text-red-500">*</span>
                                </label>
                                <select name="category_id" required
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    <option value="">Select category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($categories->isEmpty())
                                    <p class="text-xs text-amber-500 mt-1">
                                        No categories yet.
                                        <a href="{{ route('categories.index') }}" class="underline">Create one →</a>
                                    </p>
                                @endif
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SKU</label>
                                <div class="flex gap-2">
                                    <input type="text" name="sku" id="sku"
                                           value="{{ old('sku') }}" placeholder="Auto-generated"
                                           class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white font-mono text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    <button type="button" onclick="generateSku()"
                                            class="px-3 py-2 text-xs bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg transition">
                                        Generate
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Description
                                </label>
                                <span class="text-xs text-gray-400">
                                    <span id="descCount">0</span> / 5000
                                </span>
                            </div>
                            <textarea name="description" id="description" rows="5" maxlength="5000"
                                      placeholder="Describe your product..."
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none">{{ old('description') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Tags <span class="text-xs text-gray-400 font-normal">(comma separated)</span>
                            </label>
                            <input type="text" name="tags" id="tags"
                                   value="{{ old('tags') }}"
                                   placeholder="wireless, bluetooth, audio, premium"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <div id="tagPreview" class="flex flex-wrap gap-2 mt-2"></div>
                        </div>
                    </div>
                </div>

                {{-- Pricing --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <span class="w-1 h-5 bg-green-500 rounded"></span>
                        Pricing
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Price <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">$</span>
                                <input type="number" step="0.01" min="0" name="price" id="price"
                                       value="{{ old('price') }}" required
                                       class="w-full pl-7 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Discount Price <span class="text-xs text-gray-400 font-normal">(optional)</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">$</span>
                                <input type="number" step="0.01" min="0" name="discount_price" id="discount_price"
                                       value="{{ old('discount_price') }}"
                                       class="w-full pl-7 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    {{-- Live discount preview --}}
                    <div id="discountPreview" class="hidden mt-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-700 dark:text-gray-300">Final price after discount:</span>
                            <div class="flex items-center gap-2">
                                <span class="text-green-600 dark:text-green-400 font-bold text-lg" id="finalPrice">$0.00</span>
                                <span class="px-2 py-0.5 bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 rounded text-xs font-medium" id="discountPercent">-0%</span>
                            </div>
                        </div>
                    </div>
                </div>


            {{-- ✨ NEW: Specifications builder --}}
            @include('products.partials.specifications', [
                'specSections'    => $specSections,
                'categorySpecMap' => $categorySpecMap,
                'existingSpecs'   => [],
            ])


                {{-- Image upload --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <span class="w-1 h-5 bg-purple-500 rounded"></span>
                        Product Image
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        {{-- Drop zone --}}
                        <div>
                            <label for="imageInput"
                                   id="dropZone"
                                   class="cursor-pointer flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700/30 hover:border-indigo-400 hover:bg-indigo-50/50 dark:hover:bg-indigo-900/10 transition">
                                <svg class="w-10 h-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Click or drag image here
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    PNG, JPG, WEBP up to 2MB
                                </p>
                                <input type="file" name="image" id="imageInput" accept="image/*" class="hidden">
                            </label>

                            <button type="button" id="removeImage" onclick="removeImage()"
                                    class="hidden mt-3 w-full px-3 py-2 text-sm text-red-600 hover:text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                Remove image
                            </button>
                        </div>

                        {{-- Preview --}}
                        <div class="flex items-center justify-center">
                            <div class="w-full h-48 bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden flex items-center justify-center border border-gray-200 dark:border-gray-600">
                                <img id="imagePreview" src="" alt="" class="hidden w-full h-full object-cover">
                                <div id="previewPlaceholder" class="text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-xs">Preview will appear here</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT: sidebar --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- Status --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 sticky top-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Publish</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                            <select name="status" id="status"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <option value="active" @selected(old('status', 'active') === 'active')>✅ Active</option>
                                <option value="draft"  @selected(old('status') === 'draft')>📝 Draft</option>
                                <option value="archived" @selected(old('status') === 'archived')>📦 Archived</option>
                            </select>
                        </div>

                        <div>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="featured" value="1" id="featured"
                                       @checked(old('featured'))
                                       class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <span class="text-sm text-gray-700 dark:text-gray-300">
                                    ⭐ Featured product
                                </span>
                            </label>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Stock Quantity <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="stock" id="stock" min="0"
                                   value="{{ old('stock', 0) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">

                            <div class="mt-2">
                                <span id="stockBadge" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300">
                                    ✅ In stock
                                </span>
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-700">

                        <div class="flex flex-col gap-2">
                            <button type="submit" id="submitBtn"
                                    class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed text-white rounded-lg text-sm font-medium transition flex items-center justify-center gap-2">
                                <svg id="submitSpinner" class="hidden animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <span id="submitText">Save Product</span>
                            </button>

                            <a href="{{ route('products.index') }}"
                               class="w-full text-center px-4 py-2.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white rounded-lg text-sm font-medium transition">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Tips --}}
                <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg p-4">
                    <p class="text-xs text-indigo-700 dark:text-indigo-300 flex items-start gap-2">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Tip: A good product image and detailed description boost conversion. Use a clear 1:1 image.</span>
                    </p>
                </div>

            </div>
        </form>

        @push('scripts')
        <script>
        (function () {
            const nameInput         = document.getElementById('name');
            const slugPreview       = document.getElementById('slugPreview');
            const descInput         = document.getElementById('description');
            const descCount         = document.getElementById('descCount');
            const priceInput        = document.getElementById('price');
            const discountInput     = document.getElementById('discount_price');
            const discountPreview   = document.getElementById('discountPreview');
            const finalPrice        = document.getElementById('finalPrice');
            const discountPercent   = document.getElementById('discountPercent');
            const stockInput        = document.getElementById('stock');
            const stockBadge        = document.getElementById('stockBadge');
            const tagsInput         = document.getElementById('tags');
            const tagPreview        = document.getElementById('tagPreview');
            const imageInput        = document.getElementById('imageInput');
            const imagePreview      = document.getElementById('imagePreview');
            const previewPlaceholder= document.getElementById('previewPlaceholder');
            const dropZone          = document.getElementById('dropZone');
            const removeImageBtn    = document.getElementById('removeImage');
            const form              = document.getElementById('productForm');
            const submitBtn         = document.getElementById('submitBtn');
            const submitSpinner     = document.getElementById('submitSpinner');
            const submitText        = document.getElementById('submitText');

            // ---------- Slug preview ----------
            nameInput.addEventListener('input', () => {
                const slug = nameInput.value.toLowerCase()
                    .trim()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');
                slugPreview.textContent = slug || '—';
            });

            // ---------- Description counter ----------
            function updateDescCount() {
                descCount.textContent = descInput.value.length;
            }
            descInput.addEventListener('input', updateDescCount);
            updateDescCount();

            // ---------- Discount preview ----------
            function updateDiscountPreview() {
                const price = parseFloat(priceInput.value) || 0;
                const discount = parseFloat(discountInput.value) || 0;

                if (discount > 0 && discount < price) {
                    const percent = Math.round(((price - discount) / price) * 100);
                    finalPrice.textContent = '$' + discount.toFixed(2);
                    discountPercent.textContent = '-' + percent + '%';
                    discountPreview.classList.remove('hidden');
                } else {
                    discountPreview.classList.add('hidden');
                }
            }
            priceInput.addEventListener('input', updateDiscountPreview);
            discountInput.addEventListener('input', updateDiscountPreview);

            // ---------- Stock badge ----------
            function updateStockBadge() {
                const stock = parseInt(stockInput.value) || 0;
                let cls, text;
                if (stock <= 0) {
                    cls = 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300';
                    text = '❌ Out of stock';
                } else if (stock <= 5) {
                    cls = 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300';
                    text = '⚠️ Low stock (' + stock + ')';
                } else {
                    cls = 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300';
                    text = '✅ In stock (' + stock + ')';
                }
                stockBadge.className = 'inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full ' + cls;
                stockBadge.textContent = text;
            }
            stockInput.addEventListener('input', updateStockBadge);
            updateStockBadge();

            // ---------- Tags preview ----------
            function updateTagPreview() {
                const tags = tagsInput.value.split(',').map(t => t.trim()).filter(Boolean);
                tagPreview.innerHTML = tags.map(t =>
                    `<span class="inline-flex items-center px-2 py-1 text-xs bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 rounded-full">#${t}</span>`
                ).join('');
            }
            tagsInput.addEventListener('input', updateTagPreview);
            updateTagPreview();

            // ---------- Image preview / drag & drop ----------
            function showImage(file) {
                if (!file || !file.type.startsWith('image/')) return;
                const reader = new FileReader();
                reader.onload = e => {
                    imagePreview.src = e.target.result;
                    imagePreview.classList.remove('hidden');
                    previewPlaceholder.classList.add('hidden');
                    removeImageBtn.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }

            imageInput.addEventListener('change', e => {
                if (e.target.files && e.target.files[0]) showImage(e.target.files[0]);
            });

            ['dragenter', 'dragover'].forEach(evt =>
                dropZone.addEventListener(evt, e => {
                    e.preventDefault();
                    dropZone.classList.add('border-indigo-500', 'bg-indigo-50');
                })
            );

            ['dragleave', 'drop'].forEach(evt =>
                dropZone.addEventListener(evt, e => {
                    e.preventDefault();
                    dropZone.classList.remove('border-indigo-500', 'bg-indigo-50');
                })
            );

            dropZone.addEventListener('drop', e => {
                const file = e.dataTransfer.files[0];
                if (file) {
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    imageInput.files = dt.files;
                    showImage(file);
                }
            });

            window.removeImage = function () {
                imageInput.value = '';
                imagePreview.src = '';
                imagePreview.classList.add('hidden');
                previewPlaceholder.classList.remove('hidden');
                removeImageBtn.classList.add('hidden');
            };

            // ---------- SKU generator ----------
            window.generateSku = function () {
                const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
                let s = 'PRD-';
                for (let i = 0; i < 8; i++) s += chars[Math.floor(Math.random() * chars.length)];
                document.getElementById('sku').value = s;
            };

            // ---------- Submit state ----------
            form.addEventListener('submit', () => {
                submitBtn.disabled = true;
                submitSpinner.classList.remove('hidden');
                submitText.textContent = 'Saving...';
            });

            // ---------- Unsaved changes warning ----------
            let dirty = false;
            form.addEventListener('input', () => { dirty = true; });
            window.addEventListener('beforeunload', e => {
                if (dirty && !submitBtn.disabled) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        })();
        </script>
        @endpush

    </x-sidebar-navbar>
@endsection