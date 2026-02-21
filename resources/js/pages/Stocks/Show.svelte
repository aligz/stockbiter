<script lang="ts">
    import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.svelte';
    import { inertia, router } from '@inertiajs/svelte';

    export let stock: any;
    export let metrics: any[];
    export let latestMetric: any;

    function calcPct(val: number, base: number) {
        if (!val || !base) return null;
        const pct = ((val - base) / base) * 100;
        return pct > 0 ? `+${pct.toFixed(1)}%` : `${pct.toFixed(1)}%`;
    }

    function formatNumber(
        num: number | string | null | undefined,
        locale = 'id-ID',
    ) {
        if (num === null || num === undefined) return '-';
        return Number(num).toLocaleString(locale);
    }

    function formatDate(dateStr: string | null | undefined) {
        if (!dateStr) return '-';
        return new Date(dateStr).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
        });
    }
</script>

<svelte:head>
    <title>{stock.symbol} - Stock Details</title>
</svelte:head>

<AppHeaderLayout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a
                    href="/"
                    use:inertia
                    class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm flex items-center"
                >
                    &larr; Back
                </a>
            </div>

            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 px-4 sm:px-0 space-y-4 sm:space-y-0"
            >
                <div>
                    <h1
                        class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100 flex items-center space-x-3"
                    >
                        <span>{stock.symbol}</span>
                        {#if stock.is_fca}
                            <span
                                class="px-1.5 py-0.5 text-[10px] font-bold rounded bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400 border border-orange-200 dark:border-orange-800"
                                >FCA</span
                            >
                        {/if}
                        {#if stock.sector}
                            <span
                                class="px-2 py-1 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 text-xs rounded-full"
                                >{stock.sector}</span
                            >
                        {/if}
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        {stock.company_name ?? 'Company details not available.'}
                    </p>
                </div>
                {#if latestMetric}
                    <div class="text-right">
                        <div
                            class="text-3xl font-bold text-gray-900 dark:text-white"
                        >
                            Rp {formatNumber(latestMetric.price)}
                        </div>
                        <div
                            class="text-sm font-medium {latestMetric.change > 0
                                ? 'text-green-500'
                                : latestMetric.change < 0
                                  ? 'text-red-500'
                                  : 'text-gray-500'}"
                        >
                            {latestMetric.change > 0 ? '+' : ''}{formatNumber(
                                latestMetric.change,
                            )} ({latestMetric.change > 0
                                ? '+'
                                : ''}{latestMetric.change_percentage}%)
                        </div>
                    </div>
                {/if}
            </div>

            {#if latestMetric}
                <div
                    class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700 mb-8 p-6"
                >
                    <h2
                        class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                    >
                        Latest Analysis ({formatDate(latestMetric.date)})
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Target R1
                            </p>
                            <p class="text-xl font-bold text-green-500">
                                {formatNumber(latestMetric.target_r1)}
                            </p>
                            <p class="text-xs text-gray-500">
                                {calcPct(
                                    latestMetric.target_r1,
                                    latestMetric.price - latestMetric.change,
                                ) ?? '-'}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Target Max
                            </p>
                            <p class="text-xl font-bold text-red-500">
                                {formatNumber(latestMetric.target_price)}
                            </p>
                            <p class="text-xs text-gray-500">
                                {calcPct(
                                    latestMetric.target_price,
                                    latestMetric.price - latestMetric.change,
                                ) ?? '-'}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Margin of Safety (MoS)
                            </p>
                            <p
                                class="text-xl font-bold {latestMetric.mos > 0
                                    ? 'text-green-500'
                                    : 'text-red-500'}"
                            >
                                {formatNumber(latestMetric.mos)}%
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Action
                            </p>
                            <p class="text-xl font-bold text-indigo-500">
                                {latestMetric.target_action ?? '-'}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Bandar Code
                            </p>
                            <p
                                class="text-xl font-bold text-gray-900 dark:text-white"
                            >
                                {latestMetric.bandar_code ?? '-'}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Bandar Status
                            </p>
                            <p
                                class="text-xl font-bold text-gray-900 dark:text-white"
                            >
                                {latestMetric.bandar_status ?? '-'}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Bandar Vol
                            </p>
                            <p
                                class="text-xl font-bold text-gray-900 dark:text-white"
                            >
                                {formatNumber(
                                    latestMetric.bandar_volume,
                                    'en-US',
                                )}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Bandar Avg
                            </p>
                            <p
                                class="text-xl font-bold text-gray-900 dark:text-white"
                            >
                                {formatNumber(latestMetric.bandar_avg_price)}
                            </p>
                        </div>
                    </div>
                </div>
            {/if}

            <div
                class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700"
            >
                <div
                    class="px-6 py-4 border-b border-gray-200 dark:border-gray-700"
                >
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-white"
                    >
                        Metrics History
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table
                        class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                    >
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th
                                    scope="col"
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >Date</th
                                >
                                <th
                                    scope="col"
                                    class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >Price</th
                                >
                                <th
                                    scope="col"
                                    class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >Change</th
                                >
                                <th
                                    scope="col"
                                    class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >Day High</th
                                >
                                <th
                                    scope="col"
                                    class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >Target R1</th
                                >
                                <th
                                    scope="col"
                                    class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >Target Max</th
                                >
                                <th
                                    scope="col"
                                    class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >Bandar Vol</th
                                >
                                <th
                                    scope="col"
                                    class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >Bandar Avg</th
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
                                    <td
                                        class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white font-medium"
                                        >{formatDate(metric.date)}</td
                                    >
                                    <td
                                        class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium {metric.change >
                                        0
                                            ? 'text-green-500'
                                            : metric.change < 0
                                              ? 'text-red-500'
                                              : 'text-gray-900 dark:text-white'}"
                                        >{formatNumber(metric.price)}</td
                                    >
                                    <td
                                        class="px-4 py-3 whitespace-nowrap text-sm text-right {metric.change_percentage >
                                        0
                                            ? 'text-green-500'
                                            : metric.change_percentage < 0
                                              ? 'text-red-500'
                                              : 'text-gray-500'}"
                                    >
                                        {metric.change > 0
                                            ? '+'
                                            : ''}{formatNumber(metric.change)} ({metric.change >
                                        0
                                            ? '+'
                                            : ''}{metric.change_percentage}%)
                                    </td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap text-sm text-right text-orange-500 font-bold"
                                        >{formatNumber(metric.day_high)}</td
                                    >
                                    <td
                                        class="px-4 py-3 whitespace-nowrap text-sm text-right font-bold text-green-500"
                                        >{formatNumber(metric.target_r1)}</td
                                    >
                                    <td
                                        class="px-4 py-3 whitespace-nowrap text-sm text-right font-bold text-red-500"
                                        >{formatNumber(metric.target_price)}</td
                                    >
                                    <td
                                        class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white"
                                        >{formatNumber(
                                            metric.bandar_volume,
                                            'en-US',
                                        )}</td
                                    >
                                    <td
                                        class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium text-gray-900 dark:text-white"
                                        >{formatNumber(
                                            metric.bandar_avg_price,
                                        )}</td
                                    >
                                </tr>
                            {:else}
                                <tr>
                                    <td
                                        colspan="8"
                                        class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 text-sm"
                                    >
                                        No metrics history available.
                                    </td>
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 text-center text-xs text-gray-400">
                <p>
                    Data is synced periodically. Target computations are
                    estimated.
                </p>
            </div>
        </div>
    </div>
</AppHeaderLayout>
