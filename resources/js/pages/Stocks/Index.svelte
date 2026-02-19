<script lang="ts">
    import AppLayout from '@/layouts/AppLayout.svelte';

    export let stocks: any[] = [];
</script>

<svelte:head>
    <title>Stock Screener</title>
</svelte:head>

<AppLayout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h2
                            class="text-2xl font-semibold leading-tight text-gray-800"
                        >
                            Adimology Stock Screener
                        </h2>
                        <!-- Future: Add Add Stock Button or Search -->
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >Symbol</th
                                    >
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >Price</th
                                    >
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >Change</th
                                    >
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >Target Price (Max)</th
                                    >
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >MOS</th
                                    >
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >Action</th
                                    >
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >Last Updated</th
                                    >
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                {#each stocks as stock}
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div
                                                class="text-sm font-medium text-gray-900"
                                            >
                                                {stock.symbol}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {stock.company_name}
                                            </div>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                        >
                                            {stock.price.toLocaleString(
                                                'id-ID',
                                            )}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm"
                                        >
                                            <span
                                                class={stock.change_percentage >=
                                                0
                                                    ? 'text-green-600'
                                                    : 'text-red-600'}
                                            >
                                                {stock.change_percentage > 0
                                                    ? '+'
                                                    : ''}{stock.change_percentage}%
                                            </span>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-bold"
                                        >
                                            {stock.target_price.toLocaleString(
                                                'id-ID',
                                            )}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm"
                                        >
                                            <span
                                                class={stock.mos > 0
                                                    ? 'text-green-600 font-bold'
                                                    : 'text-gray-500'}
                                            >
                                                {stock.mos}%
                                            </span>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm"
                                        >
                                            <span
                                                class={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                ${stock.target_action === 'Accumulate' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}`}
                                            >
                                                {stock.target_action}
                                            </span>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                        >
                                            {stock.last_updated}
                                        </td>
                                    </tr>
                                {:else}
                                    <tr>
                                        <td
                                            colspan="7"
                                            class="px-6 py-4 text-center text-gray-500"
                                        >
                                            No stocks tracked yet. Run <code
                                                >php artisan stock:sync SYMBOL</code
                                            > to add data.
                                        </td>
                                    </tr>
                                {/each}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</AppLayout>
