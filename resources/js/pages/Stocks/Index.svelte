<script lang="ts">
    import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.svelte';
    import { router, inertia } from '@inertiajs/svelte';
    import {
        store as storeRoute,
        destroy as destroyRoute,
    } from '@/actions/App/Http/Controllers/StockController';

    export let stocks: any[] = [];
    export let errors: any = {};

    let symbol = '';
    let processing = false;
    let searchQuery = '';

    $: filteredStocks = stocks.filter(
        (s) =>
            s.symbol.toLowerCase().includes(searchQuery.toLowerCase()) ||
            (s.company_name &&
                s.company_name
                    .toLowerCase()
                    .includes(searchQuery.toLowerCase())) ||
            (s.sector &&
                s.sector.toLowerCase().includes(searchQuery.toLowerCase())),
    );

    function submit() {
        processing = true;
        router.post(
            storeRoute.url(),
            { symbol },
            {
                onSuccess: () => {
                    symbol = '';
                },
                onFinish: () => {
                    processing = false;
                },
            },
        );
    }

    function remove(stock: any) {
        if (confirm(`Are you sure you want to remove ${stock.symbol}?`)) {
            router.delete(destroyRoute.url(stock.id));
        }
    }

    function calcPct(val: number, base: number) {
        if (!val || !base) return null;
        const pct = ((val - base) / base) * 100;
        return pct > 0 ? `+${pct.toFixed(1)}%` : `${pct.toFixed(1)}%`;
    }
</script>

<svelte:head>
    <title>Stock Watchlist</title>
</svelte:head>

<AppHeaderLayout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 px-4 sm:px-0">
                <h1
                    class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100"
                >
                    Stock Watchlist
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Calculated based on Adimology's stock prediction.
                </p>
            </div>

            <div
                class="mb-4 flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0 px-4 sm:px-0"
            >
                <div class="w-full md:w-1/3 relative">
                    <input
                        type="text"
                        bind:value={searchQuery}
                        maxlength="4"
                        pattern="[a-zA-Z]+"
                        placeholder="Search by symbol, name, or sector..."
                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:text-white px-3 py-2 shadow-sm"
                    />
                </div>

                <form
                    on:submit|preventDefault={submit}
                    class="flex items-center space-x-2 w-full md:w-auto relative"
                >
                    <div class="rounded-md shadow-sm w-full md:w-auto">
                        <input
                            type="text"
                            bind:value={symbol}
                            maxlength="4"
                            pattern="[a-zA-Z]+"
                            placeholder="Add Stock (e.g BBCA)"
                            class="block w-full uppercase rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:text-white px-3 py-2 shadow-sm"
                        />
                    </div>
                    <button
                        type="submit"
                        disabled={processing}
                        class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 h-[38px] whitespace-nowrap"
                    >
                        {processing ? '...' : '+ Add'}
                    </button>
                    {#if errors?.symbol}
                        <div
                            class="absolute top-10 right-0 text-xs text-red-600"
                        >
                            {errors.symbol}
                        </div>
                    {/if}
                </form>
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
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >Date ↓</th
                                >
                                <th
                                    scope="col"
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >Emiten</th
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
                                <th
                                    scope="col"
                                    class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    ><span class="sr-only">Manage</span></th
                                >
                            </tr>
                        </thead>
                        <tbody
                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            {#each filteredStocks as stock}
                                <tr
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/25 transition-colors"
                                >
                                    <td
                                        class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        {stock.last_updated}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <a
                                                href="/stocks/{stock.symbol}"
                                                use:inertia
                                                class="text-base font-bold text-purple-600 dark:text-purple-400 hover:underline"
                                                >{stock.symbol}</a
                                            >
                                            <span
                                                class="text-xs text-gray-500 dark:text-gray-400 truncate w-32"
                                                >{stock.sector ??
                                                    stock.company_name ??
                                                    '-'}</span
                                            >
                                        </div>
                                    </td>
                                    <td
                                        class="px-4 py-4 whitespace-nowrap text-right text-base font-bold text-gray-900 dark:text-white"
                                    >
                                        {stock.prev_close
                                            ? Number(
                                                  stock.prev_close,
                                              ).toLocaleString('id-ID')
                                            : '-'}
                                    </td>
                                    <td
                                        class="px-4 py-4 whitespace-nowrap text-right"
                                    >
                                        <div class="flex flex-col">
                                            <span
                                                class="text-base font-bold text-green-500"
                                                >{stock.target_r1
                                                    ? Number(
                                                          stock.target_r1,
                                                      ).toLocaleString('id-ID')
                                                    : '-'}</span
                                            >
                                            <span
                                                class="text-xs text-gray-500 dark:text-gray-400"
                                                >{calcPct(
                                                    stock.target_r1,
                                                    stock.prev_close,
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
                                                >{stock.target_max
                                                    ? Number(
                                                          stock.target_max,
                                                      ).toLocaleString('id-ID')
                                                    : '-'}</span
                                            >
                                            <span
                                                class="text-xs text-gray-500 dark:text-gray-400"
                                                >{calcPct(
                                                    stock.target_max,
                                                    stock.prev_close,
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
                                                >{stock.day_high
                                                    ? Number(
                                                          stock.day_high,
                                                      ).toLocaleString('id-ID')
                                                    : '-'}</span
                                            >
                                            <span
                                                class="text-xs text-gray-500 dark:text-gray-400"
                                                >{calcPct(
                                                    stock.day_high,
                                                    stock.prev_close,
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
                                                >{stock.close
                                                    ? Number(
                                                          stock.close,
                                                      ).toLocaleString('id-ID')
                                                    : '-'}</span
                                            >
                                            <span
                                                class="text-xs text-gray-500 dark:text-gray-400"
                                                >{calcPct(
                                                    stock.close,
                                                    stock.prev_close,
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
                                                >{stock.bandar_code ??
                                                    '-'}</span
                                            >
                                            {#if stock.bandar_status}
                                                <span
                                                    class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                                >
                                                    {stock.bandar_status}
                                                </span>
                                            {/if}
                                        </div>
                                    </td>
                                    <td
                                        class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        {stock.bandar_vol
                                            ? Number(
                                                  stock.bandar_vol,
                                              ).toLocaleString('en-US')
                                            : '-'}
                                    </td>
                                    <td
                                        class="px-4 py-4 whitespace-nowrap text-right"
                                    >
                                        <div class="flex flex-col">
                                            <span
                                                class="text-base font-bold text-gray-900 dark:text-white"
                                                >{stock.bandar_avg
                                                    ? Number(
                                                          stock.bandar_avg,
                                                      ).toLocaleString('id-ID')
                                                    : '-'}</span
                                            >
                                            <span
                                                class="text-xs text-gray-500 dark:text-gray-400"
                                                >{calcPct(
                                                    stock.prev_close,
                                                    stock.bandar_avg,
                                                ) ?? '-'}</span
                                            >
                                        </div>
                                    </td>
                                    <td
                                        class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium"
                                    >
                                        <button
                                            on:click={() => remove(stock)}
                                            class="text-red-500 hover:text-red-600 transition-colors"
                                            title="Delete"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            {:else}
                                <tr>
                                    <td
                                        colspan="11"
                                        class="px-6 py-12 text-center text-gray-500 dark:text-gray-400"
                                    >
                                        <div
                                            class="flex flex-col items-center justify-center space-y-2"
                                        >
                                            <p class="text-base font-medium">
                                                {#if stocks.length === 0}
                                                    No stocks in watchlist
                                                {:else}
                                                    No matching stocks found
                                                {/if}
                                            </p>
                                            <p class="text-sm">
                                                {#if stocks.length === 0}
                                                    Add a symbol above to start
                                                    tracking
                                                {:else}
                                                    Try a different search term
                                                {/if}
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
