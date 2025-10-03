<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detail Order
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ $order->order_number }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $order->getStatusBadgeClass() }}">
                    {{ $order->status }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{ tab: '{{ session('active_tab', 'info') }}', purchaseMode: 'single', showInvoiceForm: false }">

            @if (session('success'))
                <div x-data="{ show: true }" x-show="show"
                    class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6 relative" role="alert">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-emerald-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-emerald-800 font-medium">{{ session('success') }}</p>
                        <button @click="show = false"
                            class="absolute top-2 right-2 text-emerald-600 hover:text-emerald-900 rounded focus:outline-none"
                            aria-label="Close">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Progress Bar Status Order -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Progress Status Order</h3>
                    @php
                        $stepNumber = \App\Models\Order::getProgressIndex($order->status) + 1;
                        $totalSteps = count(\App\Models\Order::PROGRESS_STATUSES);
                    @endphp
                    <span class="text-sm text-gray-500">Step {{ $stepNumber }} dari {{ $totalSteps }}</span>
                </div>

                <div class="relative px-6" x-data="{ 
                    currentIndex: {{ \App\Models\Order::getProgressIndex($order->status) }},
                    totalSteps: {{ count(\App\Models\Order::PROGRESS_STATUSES) }},
                    getProgressPercentage() {
                        if (this.currentIndex === 0) return 0;
                        // Calculate precise positioning to center on each node
                        const nodeWidth = (100 / this.totalSteps);
                        const nodeCenterOffset = nodeWidth / 2;
                        return (this.currentIndex * nodeWidth) + nodeCenterOffset;
                    },
                    getCompletedWidth() {
                        // Width from start to center of last completed step
                        if (this.currentIndex === 0) return '0%';
                        const stepWidth = 100 / (this.totalSteps - 1);
                        return (this.currentIndex * stepWidth) + '%';
                    }
                }">
                    @php
                        $statuses = \App\Models\Order::PROGRESS_STATUSES;
                        $statusLabels = ['Draft', 'Menunggu', 'Produksi', 'Selesai', 'Dikirim', 'Closed'];
                        $currentIndex = \App\Models\Order::getProgressIndex($order->status);
                        $totalSteps = count($statuses);
                    @endphp

                    <!-- Steps Container using CSS Grid for perfect alignment -->
                    <div class="grid grid-cols-6 gap-0 relative">
                        <!-- Progress Line Background -->
                        <div class="absolute top-4 left-4 right-4 h-0.5 bg-gray-200 z-10"></div>

                        <!-- Completed Progress Line (Green) - from start to last completed step -->
                        <div x-show="currentIndex > 0" 
                             class="absolute top-4 left-4 h-0.5 bg-emerald-500 z-15 transition-all duration-500"
                             :style="`width: ${getCompletedWidth()}; max-width: calc(100% - 32px);`"></div>

                        <!-- Current Progress Line with gradient -->
                        <div x-show="currentIndex > 0"
                             class="absolute top-4 left-4 h-0.5 bg-gradient-to-r from-emerald-500 to-blue-500 z-20 transition-all duration-700 ease-out"
                             :style="`width: ${getProgressPercentage()}%; max-width: calc(100% - 32px);`"
                             x-init="$nextTick(() => $el.style.width = getProgressPercentage() + '%')"></div>

                        @foreach ($statuses as $index => $status)
                            @php
                                $circleClass =
                                    'w-8 h-8 mx-auto rounded-full flex items-center justify-center text-xs font-semibold border-2 relative z-20 transition-all duration-300 ease-in-out';
                                if ($index <= $currentIndex) {
                                    if ($index == $currentIndex) {
                                        $circleClass .= ' bg-blue-600 text-white border-blue-600 ring-4 ring-blue-100 shadow-lg transform scale-105';
                                    } else {
                                        $circleClass .= ' bg-emerald-500 text-white border-emerald-500 shadow-md animate-bounce';
                                    }
                                } else {
                                    $circleClass .= ' bg-white text-gray-400 border-gray-300 hover:border-gray-400 hover:shadow-md';
                                }
                                $label = $statusLabels[$index] ?? $status;
                            @endphp

                            <div class="flex flex-col items-center relative group" 
                                 x-data="{ isActive: {{ $index }} === currentIndex, isCompleted: {{ $index }} < currentIndex }">
                                <div class="{{ $circleClass }}" 
                                     :class="{ 
                                         'animate-pulse': isActive,
                                         'hover:scale-110': !isActive && !isCompleted 
                                     }"
                                     title="{{ $status }}">
                                    @if ($index < $currentIndex)
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    @elseif ($index == $currentIndex)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="10" stroke-width="3" opacity="0.25"></circle>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  :d="`M12 ${12 - 4} A ${+index + 2} ${+index + 2} 0 1 1 ${12} ${12 + 4}`"></path>
                                        </svg>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </div>

                                <div class="text-center mt-2 w-full" 
                                     :class="{ 'animate-pulse': isActive }">
                                    <span class="text-[10px] text-gray-600 font-medium block truncate transition-colors duration-300 cursor-help"
                                          :class="{ 'text-blue-600 font-semibold': isActive, 'text-emerald-600 font-semibold': isCompleted }"
                                          title="{{ $status }}">
                                        {{ $label }}
                                    </span>
                                    <!-- Status indicator dot -->
                                    <div class="mt-1 h-1 w-1 mx-auto rounded-full transition-all duration-300"
                                         :class="{ 
                                             'bg-blue-500': isActive, 
                                             'bg-emerald-500': isCompleted,
                                             'bg-gray-300': !isActive && !isCompleted 
                                         }">
                                        <div x-show="isCompleted" class="h-1 w-1 bg-emerald-400 rounded-full animate-ping"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Progress Summary -->
                    <div class="mt-6 text-center space-y-2">
                        <p class="text-sm text-gray-600">
                            <span class="font-medium">Status saat ini:</span>
                            <span class="text-blue-600 font-semibold">{{ $order->status }}</span>
                        </p>
                        <div class="flex justify-center items-center space-x-4 text-xs">
                            <div class="flex items-center space-x-1">
                                <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                                <span class="text-gray-500">Selesai</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                                <span class="text-gray-500">Sedang Proses</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                                <span class="text-gray-500">Menunggu</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs - Responsive dengan Dropdown -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6">
                <!-- Desktop Tabs -->
                <nav class="hidden md:flex space-x-1 p-2" aria-label="Tabs">
                    <!-- Tab buttons sama seperti sebelumnya untuk desktop -->
                    <button @click="tab = 'info'"
                        :class="{ 'bg-blue-50 text-blue-700 border-blue-200': tab === 'info', 'text-gray-500 hover:text-gray-700 hover:bg-gray-50': tab !== 'info' }"
                        class="flex-1 px-4 py-3 text-sm font-medium rounded-xl border-2 transition-all duration-200">
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Info Order</span>
                        </div>
                    </button>
                    <button @click="tab = 'pemasukan'"
                        :class="{ 'bg-blue-50 text-blue-700 border-blue-200': tab === 'pemasukan', 'text-gray-500 hover:text-gray-700 hover:bg-gray-50': tab !== 'pemasukan' }"
                        class="flex-1 px-4 py-3 text-sm font-medium rounded-xl border-2 transition-all duration-200">
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                </path>
                            </svg>
                            <span>Pemasukan</span>
                        </div>
                    </button>
                    <button @click="tab = 'invoice'"
                        :class="{ 'bg-blue-50 text-blue-700 border-blue-200': tab === 'invoice', 'text-gray-500 hover:text-gray-700 hover:bg-gray-50': tab !== 'invoice' }"
                        class="flex-1 px-4 py-3 text-sm font-medium rounded-xl border-2 transition-all duration-200">
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <span>Invoice</span>
                        </div>
                    </button>
                    <button @click="tab = 'pembelian'"
                        :class="{ 'bg-blue-50 text-blue-700 border-blue-200': tab === 'pembelian', 'text-gray-500 hover:text-gray-700 hover:bg-gray-50': tab !== 'pembelian' }"
                        class="flex-1 px-4 py-3 text-sm font-medium rounded-xl border-2 transition-all duration-200">
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <span>Pembelian</span>
                        </div>
                    </button>
                    <button @click="tab = 'biaya'"
                        :class="{ 'bg-blue-50 text-blue-700 border-blue-200': tab === 'biaya', 'text-gray-500 hover:text-gray-700 hover:bg-gray-50': tab !== 'biaya' }"
                        class="flex-1 px-4 py-3 text-sm font-medium rounded-xl border-2 transition-all duration-200">
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                </path>
                            </svg>
                            <span>Biaya Produksi</span>
                        </div>
                    </button>
                    <button @click="tab = 'ringkasan'"
                        :class="{ 'bg-blue-50 text-blue-700 border-blue-200': tab === 'ringkasan', 'text-gray-500 hover:text-gray-700 hover:bg-gray-50': tab !== 'ringkasan' }"
                        class="flex-1 px-4 py-3 text-sm font-medium rounded-xl border-2 transition-all duration-200">
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                            <span>Ringkasan</span>
                        </div>
                    </button>
                </nav>

                <!-- Mobile Dropdown -->
                <div class="md:hidden p-2" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium bg-gray-50 rounded-xl border-2 border-gray-200">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span x-text="getTabName(tab)">Info Order</span>
                        </div>
                        <svg class="w-5 h-5 transform transition-transform" :class="{ 'rotate-180': open }"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition
                        class="mt-2 bg-white border border-gray-200 rounded-xl shadow-lg">
                        <button @click="tab = 'info'; open = false"
                            class="w-full flex items-center px-4 py-3 text-sm hover:bg-gray-50 first:rounded-t-xl">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Info Order
                        </button>
                        <button @click="tab = 'pemasukan'; open = false"
                            class="w-full flex items-center px-4 py-3 text-sm hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                </path>
                            </svg>
                            Pemasukan
                        </button>
                        <button @click="tab = 'invoice'; open = false"
                            class="w-full flex items-center px-4 py-3 text-sm hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Invoice
                        </button>
                        <button @click="tab = 'pembelian'; open = false"
                            class="w-full flex items-center px-4 py-3 text-sm hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            Pembelian
                        </button>
                        <button @click="tab = 'biaya'; open = false"
                            class="w-full flex items-center px-4 py-3 text-sm hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                </path>
                            </svg>
                            Biaya Produksi
                        </button>
                        <button @click="tab = 'ringkasan'; open = false"
                            class="w-full flex items-center px-4 py-3 text-sm hover:bg-gray-50 last:rounded-b-xl">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                            Ringkasan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tab Content -->
            <div x-show="tab === 'info'" class="space-y-6">
                <!-- Info Cards -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Customer & Product Info -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Informasi Customer & Produk</h3>
                        </div>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-600">Customer</span>
                                <span
                                    class="text-sm text-gray-900 font-medium">{{ $order->customer->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-600">Produk</span>
                                <span class="text-sm text-gray-900 font-medium">{{ $order->product_name }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-600">Spesifikasi</span>
                                <span class="text-sm text-gray-900">{{ $order->product_specification ?: '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3">
                                <span class="text-sm font-medium text-gray-600">Jumlah</span>
                                <span class="text-sm text-gray-900 font-medium">{{ $order->quantity }} pcs</span>
                            </div>
                        </div>
                    </div>

                    <!-- Order Details -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Detail Order</h3>
                        </div>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-600">Tanggal Order</span>
                                <span
                                    class="text-sm text-gray-900 font-medium">{{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-600">Deadline</span>
                                <span class="text-sm text-gray-900 font-medium">
                                    {{ $order->deadline ? \Carbon\Carbon::parse($order->deadline)->format('d M Y') : '-' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-3">
                                <span class="text-sm font-medium text-gray-600">Status</span>
                                <span
                                    class="px-3 py-1 rounded-full text-sm font-medium {{ $order->getStatusBadgeClass() }}">
                                    {{ $order->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Cards -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Update Status -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Update Status Order</h3>
                        </div>

                        <form action="{{ route('orders.updateStatus', $order) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PATCH')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status Baru</label>
                                <select name="status"
                                    class="w-full border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    @foreach (\App\Models\Order::STATUSES as $status)
                                        <option value="{{ $status }}"
                                            {{ $order->status == $status ? 'selected' : '' }}>
                                            {{ $status }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit"
                                class="w-full bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                Update Status
                            </button>
                        </form>
                    </div>

                    <!-- Update Price -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Update Harga Jual</h3>
                        </div>

                        <form action="{{ route('orders.updatePrice', $order) }}" method="POST" class="space-y-4"
                            onsubmit="return validatePriceForm(this)">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="current_tab" value="info">
                            <input type="hidden" name="timestamp" value="{{ time() }}">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Harga per Unit</label>
                                <div class="relative">
                                    <span
                                        class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                                    <input type="text" name="total_price" id="total_price_input"
                                        value="{{ $order->total_price ? number_format($order->total_price, 0, ',', '.') : '' }}"
                                        class="w-full pl-12 pr-4 py-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="0" oninput="formatNumber(this)">
                                </div>
                            </div>
                            <button type="submit"
                                class="w-full bg-amber-600 text-white px-4 py-2 rounded-xl hover:bg-amber-700 focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-colors">
                                Update Harga
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div x-show="tab === 'pemasukan'" class="space-y-6">
                <!-- Data Table Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Daftar Pemasukan</h3>
                        </div>
                        @if ($order->incomes->count() > 0)
                            <div class="bg-emerald-50 px-4 py-2 rounded-xl">
                                <p class="text-sm font-semibold text-emerald-900">
                                    Total: Rp {{ number_format($order->incomes->sum('amount'), 0, ',', '.') }}
                                </p>
                            </div>
                        @endif
                    </div>

                    @if ($order->incomes->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th
                                            class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Jenis Pemasukan</th>
                                        <th
                                            class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Tanggal</th>
                                        <th
                                            class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Metode Pembayaran</th>
                                        <th
                                            class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Jumlah</th>
                                        <th
                                            class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($order->incomes as $income)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="py-4 px-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $income->type }}
                                                </div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="text-sm text-gray-600">
                                                    {{ \Carbon\Carbon::parse($income->date)->format('d M Y') }}</div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="text-sm text-gray-600">
                                                    {{ $income->payment_method_display ?? '-' }}</div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="text-sm font-medium text-emerald-900">Rp
                                                    {{ number_format($income->amount, 0, ',', '.') }}</div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <form action="{{ route('incomes.destroy', $income) }}" method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus pemasukan ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="current_tab" value="pemasukan">
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div
                                class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                    </path>
                                </svg>
                            </div>
                            <p class="text-gray-500 text-lg font-medium">Belum ada data pemasukan</p>
                            <p class="text-gray-400 text-sm mt-1">Tambahkan pemasukan pertama untuk order ini</p>
                        </div>
                    @endif
                </div>

                <!-- Add Income Form Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Tambah Pemasukan</h3>
                    </div>

                    <form action="{{ route('incomes.store', $order) }}" method="POST"
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Pemasukan</label>
                            <select name="type"
                                class="w-full border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                required>
                                <option value="">-- Pilih --</option>
                                <option value="DP">DP</option>
                                <option value="Cicilan">Cicilan</option>
                                <option value="Lunas">Lunas</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                            <input type="date" name="date"
                                class="w-full border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                            <select name="payment_method"
                                class="w-full border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">-- Pilih Metode --</option>
                                <option value="transfer">Transfer</option>
                                <option value="cash">Cash</option>
                                <option value="transfer BCA">Transfer BCA</option>
                                <option value="transfer BRI">Transfer BRI</option>
                                <option value="transfer Mandiri">Transfer Mandiri</option>
                                <option value="transfer paypal">Transfer PayPal</option>
                                <option value="E-wallet">E-Wallet</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                            <div class="relative">
                                <span
                                    class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                                <input type="text" name="amount"
                                    class="w-full pl-12 pr-4 py-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="0" required oninput="formatNumber(this)" />
                            </div>
                        </div>

                        <!-- Payment Summary -->
                        <div class="lg:col-span-4">
                            <div class="bg-gray-50 rounded-xl p-4 mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Total Harga Jual:</span>
                                        @if ($order->total_price && $order->total_price > 0)
                                            <span class="font-medium text-gray-900">Rp
                                                {{ number_format($order->total_price * $order->quantity, 0, ',', '.') }}</span>
                                        @else
                                            <span class="font-medium text-gray-500">Belum ditentukan</span>
                                        @endif
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Total Pemasukan:</span>
                                        <span class="font-medium text-emerald-600">Rp
                                            {{ number_format($order->incomes->sum('amount'), 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Sisa Pembayaran:</span>
                                        @php
                                            $sisaBayarClass = 'font-medium';
                                            if ($order->total_price && $order->total_price > 0) {
                                                $sisaBayarAmount =
                                                    $order->total_price * $order->quantity -
                                                    $order->incomes->sum('amount');
                                                if ($sisaBayarAmount <= 0) {
                                                    $sisaBayarClass .= ' text-emerald-600';
                                                } else {
                                                    $sisaBayarClass .= ' text-red-600';
                                                }
                                            } else {
                                                $sisaBayarAmount = 0;
                                                $sisaBayarClass .= ' text-gray-500';
                                            }
                                        @endphp
                                        <span class="{{ $sisaBayarClass }}">
                                            Rp {{ number_format($sisaBayarAmount, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-4">
                            <button type="submit"
                                class="w-full bg-emerald-600 text-white px-6 py-3 rounded-xl hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors font-medium">
                                Tambah Pemasukan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div x-show="tab === 'invoice'" class="space-y-6">
                <!-- Invoice List Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Daftar Invoice</h3>
                        </div>
                    </div>

                    @if ($order->invoices->count() > 0)
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            @foreach ($order->invoices as $invoice)
                                <div class="border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h4 class="font-semibold text-lg text-gray-900">
                                                {{ $invoice->invoice_number }}</h4>
                                            <div class="flex items-center space-x-4 text-sm text-gray-600 mt-1">
                                                <span>Tanggal:
                                                    {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</span>
                                                <span>Jatuh Tempo:
                                                    {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</span>
                                            </div>
                                        </div>
                                        @php
                                            $invoiceStatusClass = 'px-3 py-1 rounded-full text-sm font-medium';
                                            if ($invoice->payment_status === 'Paid') {
                                                $invoiceStatusClass .= ' bg-emerald-100 text-emerald-800';
                                            } elseif ($invoice->payment_status === 'Partial') {
                                                $invoiceStatusClass .= ' bg-amber-100 text-amber-800';
                                            } elseif ($invoice->payment_status === 'Unpaid') {
                                                $invoiceStatusClass .= ' bg-red-100 text-red-800';
                                            } else {
                                                $invoiceStatusClass .= ' bg-gray-100 text-gray-800';
                                            }
                                        @endphp
                                        <span class="{{ $invoiceStatusClass }}">
                                            {{ $invoice->payment_status_display }}
                                        </span>
                                    </div>

                                    <div class="mb-4">
                                        @if ($invoice->order->product_type === 'custom')
                                            @php
                                                $totalPembelian = $invoice->order->purchases->sum(function ($purchase) {
                                                    return $purchase->quantity * $purchase->price;
                                                });
                                                $totalBiayaProduksi = $invoice->order->productionCosts->sum('amount');
                                                $totalHPP = $totalPembelian + $totalBiayaProduksi;
                                            @endphp
                                            @if ($totalHPP > 0)
                                                <p class="text-2xl font-bold text-blue-600">
                                                    HPP: Rp {{ number_format($totalHPP, 0, ',', '.') }}
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    + Margin (akan ditentukan)
                                                </p>
                                            @else
                                                <p class="text-2xl font-bold text-gray-500">
                                                    Rp 0
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    Harga akan dihitung setelah produksi selesai
                                                </p>
                                            @endif
                                        @else
                                            <p class="text-2xl font-bold text-emerald-600">
                                                Rp {{ number_format($invoice->order->total_price, 0, ',', '.') }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                Total Invoice: Rp
                                                {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                            </p>
                                        @endif
                                        @if ($invoice->payment_status !== 'Unpaid')
                                            <p class="text-sm text-gray-600">
                                                Dibayar: Rp
                                                {{ number_format($invoice->order->incomes->sum('amount'), 0, ',', '.') }}
                                                @php
                                                    $totalOrderValue =
                                                        $invoice->order->product_type === 'custom'
                                                            ? ($invoice->subtotal > 0
                                                                ? $invoice->subtotal
                                                                : 0)
                                                            : $invoice->order->total_price * $invoice->order->quantity;
                                                    $totalPaid = $invoice->order->incomes->sum('amount');
                                                    $remainingAmount = $totalOrderValue - $totalPaid;
                                                @endphp
                                                @if ($remainingAmount > 0)
                                                    <span class="text-red-600">(Sisa: Rp
                                                        {{ number_format($remainingAmount, 0, ',', '.') }})</span>
                                                @endif
                                            </p>
                                        @endif
                                    </div>

                                    <div class="flex space-x-2">
                                        <a href="{{ route('invoices.show', $invoice) }}"
                                            class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors text-sm font-medium text-center">
                                            Detail
                                        </a>
                                        @if ($invoice->payment_status !== 'Paid')
                                            <a href="{{ route('invoices.download', $invoice) }}"
                                                class="flex-1 bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors text-sm font-medium text-center">
                                                Download
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div
                                class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <p class="text-gray-500 text-lg font-medium mb-4">Belum ada invoice untuk order ini</p>
                            @if ($order->incomes->count() > 0)
                                <button @click="showInvoiceForm = true"
                                    class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors font-medium">
                                    Buat Invoice Baru
                                </button>
                            @else
                                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 max-w-md mx-auto">
                                    <p class="text-sm text-amber-800">
                                        Invoice dapat dibuat setelah ada input pemasukan (DP/Cicilan/Lunas).
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Generate Invoice Form Card -->
                <div x-show="showInvoiceForm" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6"
                    x-data="{
                        activeSection: 'basic',
                        sections: ['basic', 'shipping', 'payment']
                    }">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Buat Invoice Baru</h3>
                        </div>
                        <button @click="showInvoiceForm = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Section Navigation -->
                    <div class="flex space-x-1 mb-6 bg-gray-100 p-1 rounded-xl">
                        <button @click="activeSection = 'basic'"
                            :class="{ 'bg-white text-blue-600 shadow-sm': activeSection === 'basic', 'text-gray-600': activeSection !== 'basic' }"
                            class="flex-1 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Dasar
                        </button>
                        <button @click="activeSection = 'shipping'"
                            :class="{ 'bg-white text-blue-600 shadow-sm': activeSection === 'shipping', 'text-gray-600': activeSection !== 'shipping' }"
                            class="flex-1 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Pengiriman
                        </button>
                        <button @click="activeSection = 'payment'"
                            :class="{ 'bg-white text-blue-600 shadow-sm': activeSection === 'payment', 'text-gray-600': activeSection !== 'payment' }"
                            class="flex-1 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Pembayaran
                        </button>
                    </div>

                    <form action="{{ route('invoices.generate', $order) }}" method="POST">
                        @csrf
                        <input type="hidden" name="current_tab" value="invoice">

                        <!-- Basic Section -->
                        <div x-show="activeSection === 'basic'" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Jatuh Tempo
                                        (Hari)</label>
                                    <input type="number" name="due_days" value="30" min="1"
                                        max="365"
                                        class="w-full border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Biaya
                                        Pengiriman</label>
                                    <div class="relative">
                                        <span
                                            class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                                        <input type="text" name="shipping_cost" value="0"
                                            class="w-full pl-12 pr-4 py-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            placeholder="0" oninput="formatNumber(this)" />
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Discount Section -->
                            <div class="border-t pt-4">
                                <h4 class="text-md font-medium text-gray-800 mb-3">Diskon</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Diskon</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                                            <input type="text" name="discount_amount" value="0"
                                                class="w-full pl-12 pr-4 py-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                placeholder="0" oninput="formatNumber(this)" />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Persentase Diskon</label>
                                        <div class="relative">
                                            <input type="number" name="discount_percentage" value=""
                                                min="0" max="100" step="0.01"
                                                class="w-full pr-8 py-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                placeholder="0.00" />
                                            <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">%</span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Diskon</label>
                                        <input type="text" name="discount_reason"
                                            class="w-full py-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            placeholder="Contoh: Early bird discount" />
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                                <textarea name="notes" rows="3"
                                    class="w-full border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="Catatan tambahan untuk invoice..."></textarea>
                            </div>
                        </div>

                        <!-- Shipping Section -->
                        <div x-show="activeSection === 'shipping'" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Pengiriman</label>
                                <textarea name="shipping_address" rows="3"
                                    class="w-full border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="Alamat pengiriman...">{{ $order->customer->address ?? '' }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pengiriman</label>
                                <input type="text" name="shipping_method"
                                    class="w-full border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="Contoh: JNE, SiCepat, dll" />
                            </div>
                        </div>

                        <!-- Payment Section -->
                        <div x-show="activeSection === 'payment'" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                                <select name="payment_method"
                                    class="w-full border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="">-- Pilih Metode --</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="cash">Cash</option>
                                    <option value="transfer BCA">Transfer BCA</option>
                                    <option value="transfer BRI">Transfer BRI</option>
                                    <option value="transfer Mandiri">Transfer Mandiri</option>
                                    <option value="transfer paypal">Transfer PayPal</option>
                                    <option value="E-wallet">E-Wallet</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Bank</label>
                                    <input type="text" name="bank_name" value="BCA"
                                        class="w-full border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Rekening</label>
                                    <input type="text" name="account_number"
                                        class="w-full border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="1234567890" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Atas Nama</label>
                                    <input type="text" name="account_holder"
                                        class="w-full border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="Nama pemilik rekening" />
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between items-center mt-6 pt-6 border-t border-gray-200">
                            <button type="button"
                                @click="activeSection = sections[Math.max(0, sections.indexOf(activeSection) - 1)]"
                                x-show="activeSection !== 'basic'"
                                class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                                ← Sebelumnya
                            </button>
                            <div class="flex space-x-3">
                                <button type="button" @click="showInvoiceForm = false"
                                    class="px-6 py-2 bg-gray-300 text-gray-700 rounded-xl hover:bg-gray-400 transition-colors">
                                    Batal
                                </button>
                                <button type="button"
                                    @click="activeSection = sections[Math.min(sections.length - 1, sections.indexOf(activeSection) + 1)]"
                                    x-show="activeSection !== 'payment'"
                                    class="px-6 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors">
                                    Selanjutnya →
                                </button>
                                <button type="submit" x-show="activeSection === 'payment'"
                                    class="px-6 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-colors">
                                    Buat Invoice
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Invoice Info Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Informasi Invoice</h3>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-blue-50 p-4 rounded-xl">
                            <h4 class="font-semibold text-blue-900 mb-3">Fitur Invoice Fleksibel:</h4>
                            <ul class="text-sm text-blue-800 space-y-2">
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-blue-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                    Invoice dapat dibuat setelah ada input pemasukan (DP/Cicilan/Lunas)
                                </li>
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-blue-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                    Otomatis sync dengan data pemasukan untuk tracking pembayaran
                                </li>
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-blue-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                    Form sederhana dengan 3 tab: Dasar, Pengiriman, Pembayaran
                                </li>
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-blue-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                    Data perusahaan dan kustom otomatis dari sistem
                                </li>
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-blue-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                    Tracking pembayaran real-time
                                </li>
                            </ul>
                        </div>

                        @if ($order->total_price)
                            <div class="bg-emerald-50 p-4 rounded-xl">
                                <h4 class="font-semibold text-emerald-900 mb-3">Harga Jual:</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-emerald-700">Harga per Unit:</span>
                                        <span class="text-lg font-bold text-emerald-800">
                                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-emerald-700">Quantity:</span>
                                        <span class="font-medium text-emerald-800">{{ $order->quantity }} pcs</span>
                                    </div>
                                    <hr class="border-emerald-200">
                                    <div class="flex justify-between">
                                        <span class="text-sm font-semibold text-emerald-700">Total:</span>
                                        <span class="text-xl font-bold text-emerald-800">
                                            Rp {{ number_format($order->total_price * $order->quantity, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    @if ($order->incomes->count() > 0)
                                        <hr class="border-emerald-200">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-emerald-700">Sudah Dibayar:</span>
                                            <span class="font-medium text-emerald-600">
                                                Rp {{ number_format($order->incomes->sum('amount'), 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @elseif($order->isCustomProduct())
                            <div class="bg-blue-50 p-4 rounded-xl">
                                <h4 class="font-semibold text-blue-900 mb-3">Produk Custom - Kalkulasi HPP:</h4>
                                @php
                                    $totalPembelian = $order->purchases->sum(function ($purchase) {
                                        return $purchase->quantity * $purchase->price;
                                    });
                                    $totalBiayaProduksi = $order->productionCosts->sum('amount');
                                    $totalHPP = $totalPembelian + $totalBiayaProduksi;
                                @endphp
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-blue-700">Total Pembelian Material:</span>
                                        <span class="font-medium text-blue-800">Rp
                                            {{ number_format($totalPembelian, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-blue-700">Total Biaya Produksi:</span>
                                        <span class="font-medium text-blue-800">Rp
                                            {{ number_format($totalBiayaProduksi, 0, ',', '.') }}</span>
                                    </div>
                                    <hr class="border-blue-200">
                                    <div class="flex justify-between">
                                        <span class="text-sm font-semibold text-blue-700">Total HPP:</span>
                                        <span class="text-lg font-bold text-blue-800">Rp
                                            {{ number_format($totalHPP, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-blue-700">Quantity:</span>
                                        <span class="font-medium text-blue-800">{{ $order->quantity }} pcs</span>
                                    </div>
                                    @if ($order->incomes->count() > 0)
                                        <hr class="border-blue-200">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-blue-700">Sudah Dibayar:</span>
                                            <span class="font-medium text-blue-600">
                                                Rp {{ number_format($order->incomes->sum('amount'), 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @endif
                                    <div class="mt-3 p-2 bg-blue-100 rounded-lg">
                                        <p class="text-xs text-blue-800">
                                            <strong>Info:</strong> Harga jual akan dihitung otomatis dari HPP + margin
                                            saat membuat invoice.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-gray-50 p-4 rounded-xl">
                                <h4 class="font-semibold text-gray-700 mb-2">Harga Jual Belum Ditentukan</h4>
                                <p class="text-sm text-gray-600">
                                    Silakan update harga jual di tab Info Order terlebih dahulu sebelum membuat invoice.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div x-show="tab === 'pembelian'" class="space-y-6">
                <!-- Data Table Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Daftar Pembelian Material</h3>
                        </div>
                        @if ($order->purchases->count() > 0)
                            <div class="bg-blue-50 px-4 py-2 rounded-xl">
                                <p class="text-sm font-semibold text-blue-900">
                                    Total: Rp
                                    {{ number_format($order->purchases->sum(function ($purchase) {return $purchase->quantity * $purchase->price;}),0,',','.') }}
                                </p>
                            </div>
                        @endif
                    </div>

                    @if ($order->purchases->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th
                                            class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Material</th>
                                        <th
                                            class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Supplier</th>
                                        <th
                                            class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Jumlah</th>
                                        <th
                                            class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Harga</th>
                                        <th
                                            class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Total</th>
                                        <th
                                            class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($order->purchases as $purchase)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="py-4 px-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $purchase->material_name }}</div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="text-sm text-gray-600">{{ $purchase->supplier }}</div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="text-sm text-gray-900">
                                                    {{ number_format($purchase->quantity, 0, ',', '.') }}</div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="text-sm text-gray-900">Rp
                                                    {{ number_format($purchase->price, 0, ',', '.') }}</div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="text-sm font-medium text-gray-900">Rp
                                                    {{ number_format($purchase->quantity * $purchase->price, 0, ',', '.') }}
                                                </div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <form action="{{ route('purchases.destroy', $purchase) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus pembelian material ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="current_tab" value="pembelian">
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div
                                class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <p class="text-gray-500 text-lg font-medium">Belum ada data pembelian</p>
                            <p class="text-gray-400 text-sm mt-1">Tambahkan pembelian material pertama untuk order ini
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Add Purchase Form Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Tambah Pembelian Material</h3>
                        </div>
                        <div class="flex space-x-2">
                            <button type="button" @click="purchaseMode = 'single'"
                                :class="{ 'bg-blue-600 text-white': purchaseMode === 'single', 'bg-gray-200 text-gray-700': purchaseMode !== 'single' }"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Single Input
                            </button>
                            <button type="button" @click="purchaseMode = 'multiple'"
                                :class="{ 'bg-blue-600 text-white': purchaseMode === 'multiple', 'bg-gray-200 text-gray-700': purchaseMode !== 'multiple' }"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Multiple Input
                            </button>
                        </div>
                    </div>

                    <!-- Single Purchase Form -->
                    <div x-show="purchaseMode === 'single'" class="space-y-4">
                        <form action="{{ route('purchases.store', $order) }}" method="POST"
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4"
                            enctype="multipart/form-data">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Material</label>
                                <input type="text" name="material_name"
                                    class="w-full border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Supplier</label>
                                <input type="text" name="supplier"
                                    class="w-full border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                                <input type="text" name="quantity"
                                    class="w-full border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="0" oninput="formatNumber(this)" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Harga per Unit</label>
                                <div class="relative">
                                    <span
                                        class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                                    <input type="text" name="price"
                                        class="w-full pl-12 pr-4 py-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="0" oninput="formatNumber(this)" />
                                </div>
                            </div>
                            <div class="md:col-span-2 lg:col-span-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Foto Nota
                                    (Opsional)</label>
                                <div class="relative">
                                    <input type="file" name="receipt_photo" accept="image/*" class="hidden"
                                        id="receipt_photo" onchange="previewFile(this, 'preview-1')" />
                                    <label for="receipt_photo"
                                        class="w-full flex items-center justify-center px-4 py-3 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-colors"
                                        id="upload-area-1">
                                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <span class="text-gray-600">Pilih file atau drag & drop</span>
                                    </label>
                                    <!-- Preview area -->
                                    <div id="preview-1" class="mt-3 hidden">
                                        <div class="relative inline-block">
                                            <img class="h-24 w-24 object-cover rounded-lg border"
                                                id="preview-img-1" />
                                            <button type="button"
                                                onclick="clearFile('receipt_photo', 'preview-1', null, 'upload-area-1')"
                                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition-colors">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1" id="preview-name-1"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="md:col-span-2 lg:col-span-4">
                                <button type="submit"
                                    class="w-full bg-emerald-600 text-white px-6 py-3 rounded-xl hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors font-medium">
                                    Tambah Pembelian
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Multiple Purchase Form -->
                    <div x-show="purchaseMode === 'multiple'" class="space-y-4" x-data="{
                        purchases: [{ material_name: '', supplier: '', quantity: '', price: '' }],
                        addRow() {
                            this.purchases.push({ material_name: '', supplier: '', quantity: '', price: '' });
                        },
                        removeRow(index) {
                            if (this.purchases.length > 1) {
                                this.purchases.splice(index, 1);
                            }
                        },
                        getTotal() {
                            return this.purchases.reduce((total, purchase) => {
                                const quantity = parseFloat(purchase.quantity.replace(/[^\d]/g, '')) || 0;
                                const price = parseFloat(purchase.price.replace(/[^\d]/g, '')) || 0;
                                return total + (quantity * price);
                            }, 0);
                        }
                    }">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-md font-semibold text-gray-900">Input Multiple Material</h4>
                            <div class="flex space-x-2">
                                <button type="button" @click="addRow()"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                                    + Tambah Baris
                                </button>
                                <button type="button"
                                    @click="purchases = [{ material_name: '', supplier: '', quantity: '', price: '' }]"
                                    class="bg-gray-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-600 transition-colors">
                                    Reset
                                </button>
                            </div>
                        </div>

                        <form action="{{ route('purchases.storeMultiple', $order) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="space-y-4">
                                <!-- Table Header -->
                                <div class="grid grid-cols-12 gap-4 bg-gray-50 p-4 rounded-xl">
                                    <div class="col-span-4">
                                        <label class="block text-sm font-semibold text-gray-700">Nama Material
                                            *</label>
                                    </div>
                                    <div class="col-span-3">
                                        <label class="block text-sm font-semibold text-gray-700">Supplier</label>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700">Jumlah *</label>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700">Harga *</label>
                                    </div>
                                    <div class="col-span-1">
                                        <label class="block text-sm font-semibold text-gray-700">Aksi</label>
                                    </div>
                                </div>

                                <!-- Dynamic Rows -->
                                <template x-for="(purchase, index) in purchases" :key="index">
                                    <div class="grid grid-cols-12 gap-4 items-end border-b border-gray-100 pb-4">
                                        <div class="col-span-4">
                                            <input type="text" :name="`purchases[${index}][material_name]`"
                                                x-model="purchase.material_name"
                                                class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                placeholder="Nama material" required />
                                        </div>
                                        <div class="col-span-3">
                                            <input type="text" :name="`purchases[${index}][supplier]`"
                                                x-model="purchase.supplier"
                                                class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                placeholder="Supplier" />
                                        </div>
                                        <div class="col-span-2">
                                            <input type="text" :name="`purchases[${index}][quantity]`"
                                                x-model="purchase.quantity"
                                                class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                placeholder="0" required oninput="formatNumber(this)" />
                                        </div>
                                        <div class="col-span-2">
                                            <div class="relative">
                                                <span
                                                    class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">Rp</span>
                                                <input type="text" :name="`purchases[${index}][price]`"
                                                    x-model="purchase.price"
                                                    class="w-full pl-8 pr-4 py-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                    placeholder="0" required oninput="formatNumber(this)" />
                                            </div>
                                        </div>
                                        <div class="col-span-1">
                                            <button type="button" @click="removeRow(index)"
                                                class="w-full bg-red-500 text-white p-2 rounded-lg hover:bg-red-600 transition-colors"
                                                :disabled="purchases.length === 1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <!-- Total Summary -->
                                <div class="bg-blue-50 p-4 rounded-xl">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-semibold text-blue-900">Total Estimasi:</span>
                                        <span class="text-lg font-bold text-blue-900"
                                            x-text="'Rp ' + getTotal().toLocaleString('id-ID')"></span>
                                    </div>
                                </div>

                                <!-- Receipt Photo -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto Nota
                                        (Opsional)</label>
                                    <div class="relative">
                                        <input type="file" name="receipt_photo" accept="image/*" class="hidden"
                                            id="receipt_photo_multiple"
                                            onchange="previewFile(this, 'preview-multiple')" />
                                        <label for="receipt_photo_multiple"
                                            class="w-full flex items-center justify-center px-4 py-3 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-colors"
                                            id="upload-area-multiple">
                                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            <span class="text-gray-600">Pilih file atau drag & drop</span>
                                        </label>
                                        <!-- Preview area -->
                                        <div id="preview-multiple" class="mt-3 hidden">
                                            <div class="relative inline-block">
                                                <img class="h-24 w-24 object-cover rounded-lg border"
                                                    id="preview-img-multiple" />
                                                <button type="button"
                                                    onclick="clearFile('receipt_photo_multiple', 'preview-multiple', null, 'upload-area-multiple')"
                                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1" id="preview-name-multiple"></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div>
                                    <button type="submit"
                                        class="w-full bg-emerald-600 text-white px-6 py-3 rounded-xl hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors font-medium">
                                        Simpan Semua Pembelian
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Card galeri foto nota -->
                {{-- <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Galeri Foto Nota</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @forelse ($order->purchases->whereNotNull('receipt_photo') as $purchase)
                            <div class="relative group border rounded-xl overflow-hidden">
                                <img src="{{ asset('storage/' . $purchase->receipt_photo) }}" alt="Foto Nota" class="object-cover w-full h-40">
                                <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-2 truncate">{{ $purchase->material_name ?? 'Nota' }}</div>
                                <!-- Tombol hapus foto nota (opsional) -->
                                <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" class="absolute top-2 right-2">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="current_tab" value="pembelian">
                                    <button type="submit" class="bg-red-600 bg-opacity-80 hover:bg-opacity-100 text-white rounded-full p-1 focus:outline-none" title="Hapus Foto Nota">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="col-span-2 text-gray-400 text-center py-8">Belum ada foto nota diupload</div>
                        @endforelse
                    </div>
                </div> --}}

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Galeri Foto Nota</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @forelse ($order->purchases->whereNotNull('receipt_photo') as $index => $purchase)
                            <div class="relative group border rounded-xl overflow-hidden cursor-pointer transform hover:scale-105 transition-all duration-200"
                                onclick="openModal('{{ asset('storage/' . $purchase->receipt_photo) }}', '{{ $purchase->material_name ?? 'Nota' }}', '{{ $purchase->created_at->format('d M Y') }}', {{ $index }})">
                                <img src="{{ asset('storage/' . $purchase->receipt_photo) }}" alt="Foto Nota"
                                    class="object-cover w-full h-40">
                                <div
                                    class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-2 truncate">
                                    {{ $purchase->material_name ?? 'Nota' }}</div>

                                <!-- Hover overlay with eye icon -->
                                <div
                                    class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 flex items-center justify-center transition-all duration-200">
                                    <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </div>

                                <!-- Delete button -->
                                <form action="{{ route('purchases.destroy', $purchase) }}" method="POST"
                                    class="absolute top-2 right-2" onclick="event.stopPropagation()">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="current_tab" value="pembelian">
                                    <button type="submit"
                                        class="bg-red-600 bg-opacity-80 hover:bg-opacity-100 text-white rounded-full p-1 focus:outline-none"
                                        title="Hapus Foto Nota"
                                        onclick="return confirm('Yakin ingin menghapus foto ini?')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="col-span-2 md:col-span-4 text-gray-400 text-center py-8">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Belum ada foto nota diupload
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Modal -->
                <div id="imageModal"
                    class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
                    <div class="relative max-w-4xl max-h-[90vh] mx-4">
                        <!-- Close Button -->
                        <button onclick="closeModal()"
                            class="absolute -top-4 -right-4 bg-white rounded-full p-2 shadow-lg hover:bg-gray-100 transition-colors z-10">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <!-- Image Container -->
                        <div class="bg-white rounded-lg shadow-2xl overflow-hidden">
                            <div class="relative">
                                <img id="modalImage" src="" alt="Detail Foto"
                                    class="w-full max-h-[70vh] object-contain">

                                <!-- Navigation Arrows (jika ada lebih dari 1 foto) -->
                                <button id="prevBtn" onclick="prevImage()"
                                    class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-80 hover:bg-opacity-100 rounded-full p-2 shadow-lg transition-all">
                                    <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <button id="nextBtn" onclick="nextImage()"
                                    class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-80 hover:bg-opacity-100 rounded-full p-2 shadow-lg transition-all">
                                    <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Image Details -->
                            <div class="p-4 bg-gray-50">
                                <h3 id="modalTitle" class="text-lg font-semibold text-gray-900 mb-2"></h3>
                                <div class="flex items-center justify-between text-sm text-gray-600">
                                    <span id="modalDate"></span>
                                    <div class="flex gap-2">
                                        <a id="downloadBtn" href="" download
                                            class="flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="tab === 'biaya'" class="space-y-6">
                <!-- Data Table Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Daftar Biaya Produksi</h3>
                        </div>
                        @if ($order->productionCosts->count() > 0)
                            <div class="bg-purple-50 px-4 py-2 rounded-xl">
                                <p class="text-sm font-semibold text-purple-900">
                                    Total: Rp {{ number_format($order->productionCosts->sum('amount'), 0, ',', '.') }}
                                </p>
                            </div>
                        @endif
                    </div>

                    @if ($order->productionCosts->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th
                                            class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Jenis Biaya</th>
                                        <th
                                            class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Deskripsi</th>
                                        <th
                                            class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Jumlah</th>
                                        <th
                                            class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($order->productionCosts as $cost)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="py-4 px-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $cost->type }}
                                                </div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="text-sm text-gray-600">{{ $cost->description ?: '-' }}
                                                </div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="text-sm font-medium text-gray-900">Rp
                                                    {{ number_format($cost->amount, 0, ',', '.') }}</div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <form action="{{ route('costs.destroy', $cost) }}" method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus biaya produksi ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="current_tab" value="biaya">
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div
                                class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                    </path>
                                </svg>
                            </div>
                            <p class="text-gray-500 text-lg font-medium">Belum ada data biaya produksi</p>
                            <p class="text-gray-400 text-sm mt-1">Tambahkan biaya produksi pertama untuk order ini</p>
                        </div>
                    @endif
                </div>

                <!-- Add Production Cost Form Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Tambah Biaya Produksi</h3>
                    </div>

                    <form action="{{ route('costs.store', $order) }}" method="POST"
                        class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Biaya</label>
                            <select name="type"
                                class="w-full border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                required>
                                <option value="">-- Pilih --</option>
                                <option value="Tenaga Kerja">Tenaga Kerja</option>
                                <option value="Overhead">Overhead</option>
                                <option value="Transportasi">Transportasi</option>
                                <option value="Biaya Pengiriman">Biaya Pengiriman</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                            <input type="text" name="description"
                                class="w-full border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="Deskripsi biaya (opsional)" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                            <div class="relative">
                                <span
                                    class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                                <input type="text" name="amount"
                                    class="w-full pl-12 pr-4 py-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="0" required oninput="formatNumber(this)" />
                            </div>
                        </div>
                        <div class="md:col-span-3">
                            <button type="submit"
                                class="w-full bg-purple-600 text-white px-6 py-3 rounded-xl hover:bg-purple-700 focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-colors font-medium">
                                Tambah Biaya Produksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div x-show="tab === 'ringkasan'" class="space-y-6">
                <!-- Financial Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @php
                        $totalPembelian = $order->purchases->sum(function ($purchase) {
                            return $purchase->quantity * $purchase->price;
                        });
                        $totalBiayaProduksi = $order->productionCosts->sum('amount');
                        $totalHPP = $totalPembelian + $totalBiayaProduksi;
                        $totalHargaJual = ($order->total_price ?? 0) * ($order->quantity ?? 1);
                        $totalPemasukan = $order->incomes->sum('amount');
                        $totalMargin = $totalHargaJual - $totalHPP;
                        $sisaBayar = $totalHargaJual - $totalPemasukan;

                        // Detailed HPP breakdown
                        $purchaseBreakdown = [];
                        foreach ($order->purchases as $purchase) {
                            $label = 'Material: ' . $purchase->material_name;
                            $purchaseBreakdown[$label] =
                                ($purchaseBreakdown[$label] ?? 0) + $purchase->quantity * $purchase->price;
                        }
                        $costBreakdown = [];
                        foreach ($order->productionCosts as $cost) {
                            $detail = trim($cost->description ?? '') !== '' ? $cost->description : $cost->type;
                            $label = 'Biaya: ' . $detail;
                            $costBreakdown[$label] = ($costBreakdown[$label] ?? 0) + $cost->amount;
                        }
                        $hppBreakdownLabels = array_keys(array_merge($purchaseBreakdown, $costBreakdown));
                        $hppBreakdownValues = array_values(array_merge($purchaseBreakdown, $costBreakdown));
                    @endphp

                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 border border-blue-200">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <h4 class="text-sm font-semibold text-blue-800">Total Pembelian Material</h4>
                        </div>
                        <p class="text-2xl font-bold text-blue-900">Rp
                            {{ number_format($totalPembelian, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-2xl p-6 border border-red-200">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-red-500 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                    </path>
                                </svg>
                            </div>
                            <h4 class="text-sm font-semibold text-red-800">Total Biaya Produksi</h4>
                        </div>
                        <p class="text-2xl font-bold text-red-900">Rp
                            {{ number_format($totalBiayaProduksi, 0, ',', '.') }}</p>
                    </div>

                    <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-2xl p-6 border border-amber-200">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                            </div>
                            <h4 class="text-sm font-semibold text-amber-800">HPP (Total)</h4>
                        </div>
                        <p class="text-2xl font-bold text-amber-900">Rp {{ number_format($totalHPP, 0, ',', '.') }}
                        </p>
                    </div>

                    <div
                        class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-2xl p-6 border border-emerald-200">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                    </path>
                                </svg>
                            </div>
                            <h4 class="text-sm font-semibold text-emerald-800">Total Harga Jual</h4>
                        </div>
                        <p class="text-2xl font-bold text-emerald-900">Rp
                            {{ number_format($totalHargaJual, 0, ',', '.') }}</p>
                    </div>

                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-6 border border-gray-200">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-gray-500 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                    </path>
                                </svg>
                            </div>
                            <h4 class="text-sm font-semibold text-gray-800">Total Pemasukan</h4>
                        </div>
                        <p class="text-2xl font-bold text-gray-900">Rp
                            {{ number_format($totalPemasukan, 0, ',', '.') }}
                        </p>
                    </div>

                    <div
                        class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-2xl p-6 border border-indigo-200">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-indigo-500 rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                    </path>
                                </svg>
                            </div>
                            <h4 class="text-sm font-semibold text-indigo-800">Sisa Pembayaran</h4>
                        </div>
                        @php
                            $sisaBayarClass = 'text-2xl font-bold';
                            if ($sisaBayar <= 0) {
                                $sisaBayarClass .= ' text-emerald-600';
                            } else {
                                $sisaBayarClass .= ' text-red-600';
                            }
                        @endphp
                        <p class="{{ $sisaBayarClass }}">Rp {{ number_format($sisaBayar, 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Breakdown HPP</h3>
                        <canvas id="hppBreakdownChart" height="120"></canvas>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Pemasukan per Tanggal</h3>
                        <canvas id="incomeTrendChart" height="120"></canvas>
                    </div>
                </div>

                <!-- Analysis Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Analisis Margin, Laba/Rugi & Status</h3>
                    </div>

                    @if ($order->isCustomProduct())
                        <!-- Margin Calculator for Custom Products -->
                        <div class="bg-blue-50 p-6 rounded-xl border border-blue-200 mb-6">
                            <h4 class="font-semibold text-blue-900 mb-4">Margin Calculator (Alat Bantu Perhitungan)
                            </h4>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-blue-700 mb-2">Margin (%)</label>
                                    <div class="relative">
                                        <input type="number" id="margin_percentage" value="30"
                                            min="0" max="100" step="0.1"
                                            class="w-full pr-8 border-blue-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            oninput="updateMarginCalculation()" />
                                        <span
                                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-500">%</span>
                                    </div>
                                    <p class="text-xs text-blue-600 mt-1">Atur margin sesuai kebutuhan untuk
                                        perhitungan</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg">
                                    <h5 class="text-sm font-semibold text-blue-900 mb-3">Hasil Perhitungan:</h5>
                                    @php
                                        $totalPembelian = $order->purchases->sum(function ($purchase) {
                                            return $purchase->quantity * $purchase->price;
                                        });
                                        $totalBiayaProduksi = $order->productionCosts->sum('amount');
                                        $totalHPP = $totalPembelian + $totalBiayaProduksi;
                                        $marginPercentage = 30;
                                        $marginAmount = $totalHPP * ($marginPercentage / 100);
                                        $totalHargaPerhitungan = $totalHPP + $marginAmount;
                                    @endphp
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Total HPP:</span>
                                            <span class="font-medium" id="total_hpp">Rp
                                                {{ number_format($totalHPP, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-blue-600">Margin (<span
                                                    id="margin_percent">{{ $marginPercentage }}</span>%):</span>
                                            <span class="text-blue-600 font-medium" id="margin_amount">Rp
                                                {{ number_format($marginAmount, 0, ',', '.') }}</span>
                                        </div>
                                        <hr class="border-blue-200">
                                        <div class="flex justify-between">
                                            <span class="text-blue-900 font-semibold">Harga Jual (Perhitungan):</span>
                                            <span class="text-blue-900 font-bold" id="total_harga_invoice">Rp
                                                {{ number_format($totalHargaPerhitungan, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 p-3 bg-blue-100 rounded-lg">
                                <p class="text-xs text-blue-800">
                                    <strong>Info:</strong> Alat bantu perhitungan margin untuk membantu penjual
                                    menentukan harga jual.
                                    Hasil perhitungan ini tidak otomatis digunakan untuk invoice.
                                    Invoice akan menggunakan margin default 30% atau sesuai input saat membuat invoice.
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="text-gray-700 font-medium">Total Margin (Laba Kotor):</span>
                                <span class="text-lg font-semibold text-gray-900">Rp
                                    {{ number_format($totalMargin, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="text-gray-700 font-medium">Laba/Rugi (Profit/Loss):</span>
                                @php
                                    $profitLossClass = 'text-lg font-bold';
                                    if ($totalMargin > 0) {
                                        $profitLossClass .= ' text-emerald-600';
                                    } elseif ($totalMargin < 0) {
                                        $profitLossClass .= ' text-red-600';
                                    } else {
                                        $profitLossClass .= ' text-gray-600';
                                    }
                                @endphp
                                <span class="{{ $profitLossClass }}">
                                    Rp {{ number_format($totalMargin, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-3">
                                <span class="text-gray-700 font-medium">Status Order:</span>
                                @if ($totalMargin > 0)
                                    <span
                                        class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 text-emerald-800 text-sm font-semibold">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        UNTUNG
                                    </span>
                                @elseif($totalMargin < 0)
                                    <span
                                        class="inline-flex items-center px-4 py-2 rounded-full bg-red-100 text-red-800 text-sm font-semibold">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        RUGI
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-4 py-2 rounded-full bg-gray-100 text-gray-800 text-sm font-semibold">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        IMPAS
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-4">
                            <h4 class="font-semibold text-gray-900 mb-3">Penjelasan Istilah:</h4>
                            <div class="space-y-2 text-sm text-gray-700">
                                <div class="flex items-start">
                                    <span class="w-2 h-2 bg-blue-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                    <span><b>HPP (Harga Pokok Produksi)</b> = Total Pembelian Material + Total Biaya
                                        Produksi Lain</span>
                                </div>
                                <div class="flex items-start">
                                    <span class="w-2 h-2 bg-blue-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                    <span><b>Total Margin</b> = Total Harga Jual - HPP (Total)</span>
                                </div>
                                <div class="flex items-start">
                                    <span class="w-2 h-2 bg-blue-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                    <span><b>Laba/Rugi</b> = Total Margin (positif = untung, negatif = rugi, nol =
                                        impas)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                // Format number input with commas
                function formatNumber(input) {
                    // Remove all non-digit characters
                    let value = input.value.replace(/[^\d]/g, '');
                    if (value) {
                        // Convert to number and format with commas
                        value = parseInt(value).toLocaleString('id-ID');
                    }
                    input.value = value;
                }

                // Margin calculation for custom products
                function updateMarginCalculation() {
                    const marginPercentage = parseFloat(document.getElementById('margin_percentage').value) || 0;
                    const totalHPP =
                        {{ $order->isCustomProduct()? $order->purchases->sum(function ($purchase) {return $purchase->quantity * $purchase->price;}) + $order->productionCosts->sum('amount'): 0 }};

                    const marginAmount = totalHPP * (marginPercentage / 100);
                    const totalHargaInvoice = totalHPP + marginAmount;

                    document.getElementById('margin_percent').textContent = marginPercentage.toFixed(1);
                    document.getElementById('margin_amount').textContent = 'Rp ' + marginAmount.toLocaleString('id-ID');
                    document.getElementById('total_harga_invoice').textContent = 'Rp ' + totalHargaInvoice.toLocaleString('id-ID');
                }

                // Validate price form before submission
                function validatePriceForm(form) {
                    console.log('Form validation called');
                    const priceInput = form.querySelector('input[name="total_price"]');
                    console.log('Price input value:', priceInput ? priceInput.value : 'No input found');

                    if (priceInput && priceInput.value) {
                        // Clean the formatted value before submission
                        const cleanValue = priceInput.value.replace(/[^\d]/g, '');
                        console.log('Cleaned value:', cleanValue);

                        if (cleanValue && parseInt(cleanValue) > 0) {
                            console.log('Validation passed, submitting form');
                            return true;
                        } else {
                            alert('Harga harus berupa angka yang valid dan lebih dari 0');
                            return false;
                        }
                    }
                    console.log('No price value, allowing submission');
                    return true; // Allow empty values
                }

                // Get current tab for redirect
                function getCurrentTab() {
                    return document.querySelector('[x-data]').__x.$data.tab;
                }

                // Add tab parameter to forms
                document.addEventListener('DOMContentLoaded', () => {
                    const forms = document.querySelectorAll('form');
                    forms.forEach(form => {
                        if (form.action.includes('/purchases') || form.action.includes('/costs') || form.action
                            .includes('/incomes')) {
                            const tabInput = document.createElement('input');
                            tabInput.type = 'hidden';
                            tabInput.name = 'current_tab';
                            tabInput.value = getCurrentTab();
                            form.appendChild(tabInput);
                        }
                    });
                });

                document.addEventListener('DOMContentLoaded', () => {
                    // HPP breakdown doughnut (detailed)
                    const hppCtx = document.getElementById('hppBreakdownChart');
                    if (hppCtx && window.Chart) {
                        const labels = @json($hppBreakdownLabels ?? []);
                        const values = @json($hppBreakdownValues ?? []);

                        // Generate palette
                        const baseColors = ['#60a5fa', '#93c5fd', '#3b82f6', '#2563eb', '#1d4ed8', '#fca5a5', '#f87171',
                            '#ef4444', '#dc2626', '#fbbf24', '#f59e0b', '#10b981', '#34d399', '#059669', '#6ee7b7',
                            '#a78bfa', '#8b5cf6', '#6366f1', '#22d3ee', '#06b6d4'
                        ];
                        const colors = values.map((_, i) => baseColors[i % baseColors.length]);

                        const data = {
                            labels,
                            datasets: [{
                                data: values,
                                backgroundColor: colors,
                                borderWidth: 0,
                            }]
                        };
                        new Chart(hppCtx, {
                            type: 'doughnut',
                            data,
                            options: {
                                plugins: {
                                    legend: {
                                        position: 'bottom'
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: (ctx) => {
                                                const label = ctx.label || '';
                                                const value = ctx.parsed || 0;
                                                const total = values.reduce((a, b) => a + b, 0) || 1;
                                                const pct = (value / total * 100).toFixed(1);
                                                return `${label}: Rp ${value.toLocaleString('id-ID')} (${pct}%)`;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }

                    // Income trend line (per tanggal input)
                    const incomeCtx = document.getElementById('incomeTrendChart');
                    if (incomeCtx && window.Chart) {
                        const incomeMap = @json(
                            $order->incomes->sortBy('date')->groupBy(function ($i) {
                                    return \Carbon\Carbon::parse($i->date)->format('Y-m-d');
                                })->map->sum('amount'));
                        const labels = Object.keys(incomeMap);
                        const values = Object.values(incomeMap);
                        new Chart(incomeCtx, {
                            type: 'line',
                            data: {
                                labels,
                                datasets: [{
                                    label: 'Pemasukan',
                                    data: values,
                                    borderColor: '#10b981',
                                    backgroundColor: 'rgba(16,185,129,0.15)',
                                    borderWidth: 2,
                                    tension: 0.3,
                                    fill: true,
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                },
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                }
                            }
                        });
                    }
                });
            </script>
            <script>
                function showFileName(input, infoId) {
                    const fileInfo = document.getElementById(infoId);
                    const fileName = document.getElementById('file-name-' + infoId.split('-')[2]);
                    const fileLabel = document.getElementById('file-label-' + infoId.split('-')[2]);

                    if (input.files && input.files[0]) {
                        fileName.textContent = input.files[0].name;
                        fileLabel.textContent = 'File dipilih';
                        fileInfo.classList.remove('hidden');

                        // Change border color to green
                        input.nextElementSibling.classList.remove('border-gray-300');
                        input.nextElementSibling.classList.add('border-green-300', 'bg-green-50');
                    }
                }

                function previewFile(input, previewId) {
                    const preview = document.getElementById(previewId);
                    const previewImg = document.getElementById('preview-img-' + previewId.split('-')[1]);
                    const previewName = document.getElementById('preview-name-' + previewId.split('-')[1]);
                    const uploadArea = document.getElementById('upload-area-' + previewId.split('-')[1]);

                    if (input.files && input.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImg.src = e.target.result;
                            previewName.textContent = input.files[0].name;
                            preview.classList.remove('hidden');

                            // Hide upload area
                            uploadArea.classList.add('hidden');
                        };
                        reader.readAsDataURL(input.files[0]);
                    }
                }

                function clearFile(inputId, infoId, labelId, uploadAreaId = null) {
                    const input = document.getElementById(inputId);
                    const fileInfo = document.getElementById(infoId);

                    // Clear input
                    input.value = '';

                    // Hide info/preview
                    fileInfo.classList.add('hidden');

                    // Reset label if exists
                    if (labelId) {
                        const fileLabel = document.getElementById(labelId);
                        fileLabel.textContent = 'Pilih file atau drag & drop';

                        // Reset border color
                        input.nextElementSibling.classList.remove('border-green-300', 'bg-green-50');
                        input.nextElementSibling.classList.add('border-gray-300');
                    }

                    // Show upload area again if exists
                    if (uploadAreaId) {
                        const uploadArea = document.getElementById(uploadAreaId);
                        uploadArea.classList.remove('hidden');
                    }
                }
            </script>
            <script>
                let currentImageIndex = 0;
                let images = [];

                // Initialize images array from Laravel data
                @if ($order->purchases->whereNotNull('receipt_photo')->count() > 0)
                    images = [
                        @foreach ($order->purchases->whereNotNull('receipt_photo') as $purchase)
                            {
                                src: '{{ asset('storage/' . $purchase->receipt_photo) }}',
                                title: '{{ $purchase->material_name ?? 'Nota' }}',
                                date: '{{ $purchase->created_at->format('d M Y') }}'
                            },
                        @endforeach
                    ];
                @endif

                function openModal(imageSrc, title, date, index) {
                    const modal = document.getElementById('imageModal');
                    const modalImage = document.getElementById('modalImage');
                    const modalTitle = document.getElementById('modalTitle');
                    const modalDate = document.getElementById('modalDate');
                    const downloadBtn = document.getElementById('downloadBtn');
                    const prevBtn = document.getElementById('prevBtn');
                    const nextBtn = document.getElementById('nextBtn');

                    currentImageIndex = index;

                    modalImage.src = imageSrc;
                    modalTitle.textContent = title;
                    modalDate.textContent = date;
                    downloadBtn.href = imageSrc;

                    // Show/hide navigation buttons
                    if (images.length <= 1) {
                        prevBtn.style.display = 'none';
                        nextBtn.style.display = 'none';
                    } else {
                        prevBtn.style.display = 'block';
                        nextBtn.style.display = 'block';
                    }

                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }

                function closeModal() {
                    const modal = document.getElementById('imageModal');
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }

                function nextImage() {
                    currentImageIndex = (currentImageIndex + 1) % images.length;
                    updateModalImage();
                }

                function prevImage() {
                    currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
                    updateModalImage();
                }

                function updateModalImage() {
                    const current = images[currentImageIndex];
                    document.getElementById('modalImage').src = current.src;
                    document.getElementById('modalTitle').textContent = current.title;
                    document.getElementById('modalDate').textContent = current.date;
                    document.getElementById('downloadBtn').href = current.src;
                }

                // Event listeners
                document.getElementById('imageModal').addEventListener('click', function(e) {
                    if (e.target === this) closeModal();
                });

                document.addEventListener('keydown', function(e) {
                    const modal = document.getElementById('imageModal');
                    if (!modal.classList.contains('hidden')) {
                        if (e.key === 'Escape') closeModal();
                        else if (e.key === 'ArrowRight') nextImage();
                        else if (e.key === 'ArrowLeft') prevImage();
                    }
                });
            </script>
            <script>
                // Tambahkan function untuk mendapatkan nama tab
                function getTabName(tab) {
                    const names = {
                        'info': 'Info Order',
                        'pembelian': 'Pembelian',
                        'biaya': 'Biaya Produksi',
                        'pemasukan': 'Pemasukan',
                        'invoice': 'Invoice',
                        'ringkasan': 'Ringkasan'
                    };
                    return names[tab] || 'Info Order';
                }
            </script>
        </div>
    </div>
</x-app-layout>
