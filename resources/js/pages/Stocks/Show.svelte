<script lang="ts">
    import { inertia } from '@inertiajs/svelte';
    import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.svelte';

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

    $: calcs = latestMetric
        ? (() => {
              const p = Number(latestMetric.price) || 0;
              const fraksi = Number(latestMetric.fraksi) || 1;
              const ara = Number(latestMetric.offer_highest) || p;
              const arb = Number(latestMetric.bid_lowest) || p;

              let totalPapan = (ara - arb) / fraksi;
              if (totalPapan === 0) totalPapan = 1;

              const totalBidInput =
                  (Number(latestMetric.total_bid_volume) || 0) / 100;
              const totalOfferInput =
                  (Number(latestMetric.total_offer_volume) || 0) / 100;

              let rata2 = (totalBidInput + totalOfferInput) / totalPapan;
              if (rata2 === 0) rata2 = 1;

              const bandarAvg = Number(latestMetric.bandar_avg_price) || 0;
              const bandarVol = Number(latestMetric.bandar_volume) || 0;

              const a = bandarAvg * 0.05;
              const p_val = rata2 > 1 ? bandarVol / rata2 : a;

              return {
                  totalPapan: Math.round(totalPapan),
                  rata2: Math.round(rata2),
                  a: Math.round(a),
                  p_val: Math.round(p_val),
              };
          })()
        : null;
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
                        <a
                            href={`https://stockbit.com/symbol/${stock.symbol}`}
                            target="_blank"
                            rel="noopener noreferrer"
                            class="hover:underline hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                        >
                            {stock.symbol}
                        </a>
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
                <div class="mb-4 flex items-center justify-between">
                    <h2
                        class="text-lg font-semibold text-gray-900 dark:text-white"
                    >
                        Latest Analysis <span
                            class="text-sm font-normal text-gray-500"
                            >({formatDate(latestMetric.date)})</span
                        >
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Top Broker Card -->
                    <div
                        class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 border border-gray-100 dark:border-gray-700"
                    >
                        <h3
                            class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-4"
                        >
                            Top Broker
                        </h3>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 mb-2"
                                >
                                    Bandar
                                </p>
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-md bg-indigo-500 text-white text-sm font-bold"
                                >
                                    {latestMetric.bandar_code ?? '-'}
                                </span>
                            </div>
                            <div>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 mb-2"
                                >
                                    Barang
                                </p>
                                <p
                                    class="text-sm font-bold text-gray-900 dark:text-white"
                                >
                                    {formatNumber(
                                        latestMetric.bandar_volume,
                                        'en-US',
                                    )}
                                    <span
                                        class="font-normal text-xs text-gray-500"
                                        >lot</span
                                    >
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 mb-2"
                                >
                                    Avg Harga
                                </p>
                                <p
                                    class="text-sm font-bold text-gray-900 dark:text-white"
                                >
                                    Rp {formatNumber(
                                        latestMetric.bandar_avg_price,
                                    )}
                                </p>
                                <p
                                    class="text-xs font-medium text-gray-500 mt-0.5"
                                >
                                    {calcPct(
                                        latestMetric.price,
                                        latestMetric.bandar_avg_price,
                                    ) ?? '-'}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Market Data Card -->
                    <div
                        class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 border border-gray-100 dark:border-gray-700"
                    >
                        <h3
                            class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-4"
                        >
                            Market Data
                        </h3>
                        <div class="grid grid-cols-3 gap-y-4 gap-x-4">
                            <div>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Harga
                                </p>
                                <p
                                    class="text-sm font-bold text-gray-900 dark:text-white"
                                >
                                    Rp {formatNumber(latestMetric.price)}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Offer Max
                                </p>
                                <p
                                    class="text-sm font-bold text-gray-900 dark:text-white"
                                >
                                    Rp {formatNumber(
                                        latestMetric.offer_highest,
                                    )}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Bid Min
                                </p>
                                <p
                                    class="text-sm font-bold text-gray-900 dark:text-white"
                                >
                                    Rp {formatNumber(latestMetric.bid_lowest)}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Fraksi
                                </p>
                                <p
                                    class="text-sm font-bold text-gray-900 dark:text-white"
                                >
                                    {formatNumber(latestMetric.fraksi)}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Total Bid
                                </p>
                                <p
                                    class="text-sm font-bold text-gray-900 dark:text-white"
                                >
                                    {formatNumber(
                                        latestMetric.total_bid_volume,
                                        'en-US',
                                    )}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Total Offer
                                </p>
                                <p
                                    class="text-sm font-bold text-gray-900 dark:text-white"
                                >
                                    {formatNumber(
                                        latestMetric.total_offer_volume,
                                        'en-US',
                                    )}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Calculations Card -->
                    <div
                        class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 border border-gray-100 dark:border-gray-700"
                    >
                        <h3
                            class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-4"
                        >
                            Calculations
                        </h3>
                        {#if calcs}
                            <div class="grid grid-cols-2 gap-y-4 gap-x-4">
                                <div>
                                    <p
                                        class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase"
                                    >
                                        Total Papan
                                    </p>
                                    <p
                                        class="text-sm font-bold text-gray-900 dark:text-white"
                                    >
                                        {formatNumber(calcs.totalPapan)}
                                    </p>
                                </div>
                                <div>
                                    <p
                                        class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase"
                                    >
                                        Rata² Bid/Offer
                                    </p>
                                    <p
                                        class="text-sm font-bold text-gray-900 dark:text-white"
                                    >
                                        {formatNumber(calcs.rata2)}
                                    </p>
                                </div>
                                <div>
                                    <p
                                        class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase"
                                    >
                                        A (5% Avg Bandar)
                                    </p>
                                    <p
                                        class="text-sm font-bold text-gray-900 dark:text-white"
                                    >
                                        {formatNumber(calcs.a)}
                                    </p>
                                </div>
                                <div>
                                    <p
                                        class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase"
                                    >
                                        P (Barang/Avg)
                                    </p>
                                    <p
                                        class="text-sm font-bold text-gray-900 dark:text-white"
                                    >
                                        {formatNumber(calcs.p_val)}
                                    </p>
                                </div>
                            </div>
                        {/if}
                    </div>

                    <!-- Target Prices Card -->
                    <div
                        class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 border border-gray-100 dark:border-gray-700"
                    >
                        <div class="flex justify-between items-center mb-4">
                            <h3
                                class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider"
                            >
                                Target Prices
                            </h3>
                            <div class="flex items-center space-x-2">
                                <span
                                    class="text-xs text-gray-500 font-medium tracking-wider"
                                    >ACTION</span
                                >
                                <span
                                    class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-800"
                                >
                                    {latestMetric.target_action ?? '-'}
                                </span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div class="flex flex-col items-center">
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide"
                                >
                                    Target Realistis
                                </p>
                                <div
                                    class="px-6 py-2 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800/50 min-w-[120px]"
                                >
                                    <p
                                        class="text-xl font-bold text-green-700 dark:text-green-400"
                                    >
                                        {formatNumber(latestMetric.target_r1)}
                                    </p>
                                </div>
                                <p
                                    class="text-xs font-bold text-green-600 dark:text-green-500 mt-2"
                                >
                                    {calcPct(
                                        latestMetric.target_r1,
                                        latestMetric.price -
                                            latestMetric.change,
                                    ) ?? '-'}
                                </p>
                            </div>
                            <div class="flex flex-col items-center">
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide"
                                >
                                    Target Max
                                </p>
                                <div
                                    class="px-6 py-2 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800/50 min-w-[120px]"
                                >
                                    <p
                                        class="text-xl font-bold text-red-700 dark:text-red-400"
                                    >
                                        {formatNumber(
                                            latestMetric.target_price,
                                        )}
                                    </p>
                                </div>
                                <p
                                    class="text-xs font-bold text-green-600 dark:text-green-500 mt-2"
                                >
                                    {calcPct(
                                        latestMetric.target_price,
                                        latestMetric.price -
                                            latestMetric.change,
                                    ) ?? '-'}
                                </p>
                            </div>
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
                                    >Harga Open</th
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
                                    >Max Harga</th
                                >
                                <th
                                    scope="col"
                                    class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >Close Harga</th
                                >
                                <th
                                    scope="col"
                                    class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >Bandar</th
                                >
                                <th
                                    scope="col"
                                    class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >Vol Bandar</th
                                >
                                <th
                                    scope="col"
                                    class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >Avg Bandar</th
                                >
                            </tr>
                        </thead>
                        <tbody
                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            {#each metrics as metric (metric.date)}
                                <tr
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/25 transition-colors"
                                >
                                    <td
                                        class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        {formatDate(metric.date)}
                                    </td>
                                    <td
                                        class="px-4 py-4 whitespace-nowrap text-right text-base font-bold text-gray-900 dark:text-white"
                                    >
                                        {formatNumber(
                                            metric.price - metric.change,
                                        )}
                                    </td>
                                    <td
                                        class="px-4 py-4 whitespace-nowrap text-right"
                                    >
                                        <div class="flex flex-col">
                                            <span
                                                class="text-base font-bold text-green-500"
                                                >{formatNumber(
                                                    metric.target_r1,
                                                )}</span
                                            >
                                            <span
                                                class="text-xs text-gray-500 dark:text-gray-400"
                                                >{calcPct(
                                                    metric.target_r1,
                                                    metric.price -
                                                        metric.change,
                                                ) ?? '-'}</span
                                            >
                                        </div>
                                    </td>
                                    <td
                                        class="px-4 py-4 whitespace-nowrap text-right"
                                    >
                                        <div class="flex flex-col">
                                            <span
                                                class="text-base font-bold text-red-500"
                                                >{formatNumber(
                                                    metric.target_price,
                                                )}</span
                                            >
                                            <span
                                                class="text-xs text-gray-500 dark:text-gray-400"
                                                >{calcPct(
                                                    metric.target_price,
                                                    metric.price -
                                                        metric.change,
                                                ) ?? '-'}</span
                                            >
                                        </div>
                                    </td>
                                    <td
                                        class="px-4 py-4 whitespace-nowrap text-right"
                                    >
                                        <div class="flex flex-col">
                                            <span
                                                class="text-base font-bold text-orange-500"
                                                >{formatNumber(
                                                    metric.day_high,
                                                )}</span
                                            >
                                            <span
                                                class="text-xs text-gray-500 dark:text-gray-400"
                                                >{calcPct(
                                                    metric.day_high,
                                                    metric.price -
                                                        metric.change,
                                                ) ?? '-'}</span
                                            >
                                        </div>
                                    </td>
                                    <td
                                        class="px-4 py-4 whitespace-nowrap text-right"
                                    >
                                        <div class="flex flex-col">
                                            <span
                                                class="text-base font-bold text-yellow-500"
                                                >{formatNumber(
                                                    metric.price,
                                                )}</span
                                            >
                                            <span
                                                class="text-xs text-gray-500 dark:text-gray-400"
                                                >{calcPct(
                                                    metric.price,
                                                    metric.price -
                                                        metric.change,
                                                ) ?? '-'}</span
                                            >
                                        </div>
                                    </td>
                                    <td
                                        class="px-4 py-4 whitespace-nowrap text-center"
                                    >
                                        <div
                                            class="flex items-center justify-center space-x-1"
                                        >
                                            <span
                                                class="text-sm font-bold text-gray-900 dark:text-white"
                                                >{metric.bandar_code ??
                                                    '-'}</span
                                            >
                                            {#if metric.bandar_status}
                                                <span
                                                    class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                                >
                                                    {metric.bandar_status}
                                                </span>
                                            {/if}
                                        </div>
                                    </td>
                                    <td
                                        class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        {formatNumber(
                                            metric.bandar_volume,
                                            'en-US',
                                        )}
                                    </td>
                                    <td
                                        class="px-4 py-4 whitespace-nowrap text-right"
                                    >
                                        <div class="flex flex-col">
                                            <span
                                                class="text-base font-bold text-gray-900 dark:text-white"
                                                >{formatNumber(
                                                    metric.bandar_avg_price,
                                                )}</span
                                            >
                                            <span
                                                class="text-xs text-gray-500 dark:text-gray-400"
                                                >{calcPct(
                                                    metric.price -
                                                        metric.change,
                                                    metric.bandar_avg_price,
                                                ) ?? '-'}</span
                                            >
                                        </div>
                                    </td>
                                </tr>
                            {:else}
                                <tr>
                                    <td
                                        colspan="9"
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
