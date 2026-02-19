<script lang="ts">
    import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.svelte';

    export let metrics: any[] = [];
</script>

<svelte:head>
    <title>Stock Metrics</title>
</svelte:head>

<AppHeaderLayout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8 px-4 sm:px-0">
                <h1
                    class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100"
                >
                    Market Opportunities
                </h1>
                <p class="text-sm text-gray-500">
                    Last updated: {metrics.length > 0
                        ? metrics[0].last_updated
                        : '-'}
                </p>
            </div>

            <div
                class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700"
            >
                <div class="overflow-x-auto">
                    <table
                        class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                    >
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th
                                    scope="col"
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >Symbol</th
                                >
                                <th
                                    scope="col"
                                    class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >Price</th
                                >
                                <th
                                    scope="col"
                                    class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >Change</th
                                >
                                <th
                                    scope="col"
                                    class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >Target Price</th
                                >
                                <th
                                    scope="col"
                                    class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >MOS</th
                                >
                                <th
                                    scope="col"
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >Action</th
                                >
                                <th
                                    scope="col"
                                    class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >Date</th
                                >
                            </tr>
                        </thead>
                        <tbody
                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            {#each metrics as metric}
                                <tr
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/25 transition-colors"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-base font-bold text-gray-900 dark:text-white"
                                                >{metric.symbol}</span
                                            >
                                            <span
                                                class="text-xs text-gray-500 dark:text-gray-400"
                                                >{metric.company_name}</span
                                            >
                                        </div>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900 dark:text-gray-200"
                                    >
                                        {Number(metric.price).toLocaleString(
                                            'id-ID',
                                        )}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-right text-sm"
                                    >
                                        <span
                                            class={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                                Number(
                                                    metric.change_percentage,
                                                ) >= 0
                                                    ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                                                    : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
                                            }`}
                                        >
                                            {Number(metric.change_percentage) >
                                            0
                                                ? '+'
                                                : ''}{Number(
                                                metric.change_percentage,
                                            )}%
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-600 dark:text-gray-300 font-medium"
                                    >
                                        {Number(
                                            metric.target_price,
                                        ).toLocaleString('id-ID')}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-right text-sm"
                                    >
                                        <span
                                            class={`font-bold ${
                                                Number(metric.mos) > 0
                                                    ? 'text-green-600 dark:text-green-400'
                                                    : 'text-gray-500 dark:text-gray-400'
                                            }`}
                                        >
                                            {Number(metric.mos)}%
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-center text-sm"
                                    >
                                        <span
                                            class={`px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${
                                                metric.target_action ===
                                                'Accumulate'
                                                    ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800'
                                                    : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600'
                                            }`}
                                        >
                                            {metric.target_action}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-400 dark:text-gray-500"
                                    >
                                        {metric.last_updated}
                                    </td>
                                </tr>
                            {:else}
                                <tr>
                                    <td
                                        colspan="7"
                                        class="px-6 py-12 text-center text-gray-500 dark:text-gray-400"
                                    >
                                        <div
                                            class="flex flex-col items-center justify-center space-y-2"
                                        >
                                            <svg
                                                class="h-10 w-10 text-gray-400"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                                />
                                            </svg>
                                            <p class="text-base font-medium">
                                                No market data available
                                            </p>
                                            <p class="text-sm">
                                                Run sync to fetch latest stock
                                                metrics
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 text-center text-xs text-gray-400">
                <p>
                    Data provided for informational purposes only. Past
                    performance is not indicative of future results.
                </p>
            </div>
        </div>
    </div>
</AppHeaderLayout>
