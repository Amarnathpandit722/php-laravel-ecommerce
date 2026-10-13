{{-- resources/views/products/partials/specifications.blade.php --}}
@props([
    'specSections',   // array from config('product_specs')
    'categorySpecMap',// [category_id => spec_key]
    'existingSpecs' => [], // ['Processor' => [['label'=>'Brand','value'=>'Intel'], ...], ...]
])

<div id="specsSection"
     x-data="specsBuilder({
        allSpecs: @js($specSections),
        categoryMap: @js($categorySpecMap),
        existing: @js($existingSpecs),
     })"
     class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">

    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
            <span class="w-1 h-5 bg-amber-500 rounded"></span>
            Product Specifications
        </h3>
        <span class="text-xs text-gray-500 dark:text-gray-400"
              x-text="Object.keys(sections).length + ' section(s)'"></span>
    </div>

    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
        Specifications adapt to the selected category. Open a section to add rows.
    </p>

    {{-- No category yet --}}
    <template x-if="!hasCategory">
        <div class="text-center py-10 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Select a category above to load the specification templates.
            </p>
        </div>
    </template>

    {{-- Sections --}}
    <div class="space-y-3" x-show="hasCategory" x-cloak>
        <template x-for="(rows, section) in sections" :key="section">
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">

                {{-- Header --}}
                <button type="button"
                        @click="toggle(section)"
                        class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 transition-transform duration-200"
                             :class="open[section] ? 'rotate-180' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                        <span class="font-medium text-gray-900 dark:text-white" x-text="section"></span>
                        <span class="text-xs text-gray-500 dark:text-gray-400"
                              x-text="'(' + rows.filter(r => r.label).length + ')'"></span>
                    </div>

                    <span class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline"
                          @click.stop="addRow(section)">
                        + Add row
                    </span>
                </button>

                {{-- Body --}}
                <div x-show="open[section]"
                     x-collapse
                     class="bg-white dark:bg-gray-800">

                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/30">
                            <tr>
                                <th class="text-left px-4 py-2 font-medium text-gray-600 dark:text-gray-300 w-1/3">Label</th>
                                <th class="text-left px-4 py-2 font-medium text-gray-600 dark:text-gray-300">Value</th>
                                <th class="w-12"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(row, idx) in rows" :key="idx">
                                <tr class="border-t border-gray-100 dark:border-gray-700">
                                    <td class="p-2 align-top">
                                        <input type="text"
                                               :name="`specs[${section}][${idx}][label]`"
                                               x-model="row.label"
                                               placeholder="e.g. Brand"
                                               class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                    </td>
                                    <td class="p-2 align-top">
                                        <input type="text"
                                               :name="`specs[${section}][${idx}][value]`"
                                               x-model="row.value"
                                               placeholder="e.g. Intel"
                                               class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                    </td>
                                    <td class="p-2 align-top text-right">
                                        <button type="button"
                                                @click="removeRow(section, idx)"
                                                class="text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition"
                                                title="Remove row">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>

                            <template x-if="rows.length === 0">
                                <tr class="border-t border-gray-100 dark:border-gray-700">
                                    <td colspan="3" class="px-4 py-6 text-center text-gray-400 text-xs">
                                        No rows yet.
                                        <button type="button" class="underline hover:text-indigo-500" @click="addRow(section)">
                                            Add one
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>
    </div>
</div>

