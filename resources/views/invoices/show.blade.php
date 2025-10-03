<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Invoice: <span class="font-mono">{{ $invoice->invoice_number }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if (session('info'))
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4" role="alert">
                    <p>{{ session('info') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Invoice Information</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-600">Invoice Number:</p>
                                <p class="font-semibold">{{ $invoice->invoice_number }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Invoice Date:</p>
                                <p class="font-semibold">
                                    {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Due Date:</p>
                                <p class="font-semibold">
                                    {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Payment Method:</p>
                                <span
                                    class="inline-block px-3 py-1 rounded-full text-sm font-medium
                                    @if ($invoice->payment_status === 'Paid') bg-green-100 text-green-800
                                    @elseif($invoice->payment_status === 'Partial') bg-amber-100 text-amber-800
                                    @elseif($invoice->payment_status === 'Unpaid') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $invoice->payment_status_display }}
                                </span>
                            </div>
                            @if ($invoice->po_number)
                            <div>
                                <p class="text-sm text-gray-600">PO Number:</p>
                                <p class="font-semibold">{{ $invoice->po_number }}</p>
                            </div>
                            @endif
                            @if ($invoice->seller_name)
                            <div>
                                <p class="text-sm text-gray-600">Seller:</p>
                                <p class="font-semibold">{{ $invoice->seller_name }}</p>
                                    </div>
                                    @endif
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Order Information</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-600">Customer:</p>
                                <p class="font-semibold">{{ $invoice->order->customer->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Product:</p>
                                <div class="flex items-start space-x-3">
                                    @if ($invoice->order->product_type === 'custom' && $invoice->order->image)
                                        <img src="{{ asset('storage/' . $invoice->order->image) }}" alt="Product Image" 
                                             class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                                    @elseif ($invoice->order->product_type !== 'custom' && $invoice->order->product && $invoice->order->product->image)
                                        <img src="{{ asset('storage/' . $invoice->order->product->image) }}" alt="Product Image" 
                                             class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                                    @else
                                        <img src="{{ asset('images/no-image.svg') }}" alt="No Image" 
                                             class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                                    @endif
                                    <div class="flex-1">
                                        <p class="font-semibold">{{ $invoice->order->product_name }}</p>
                                        @if ($invoice->order->product_type === 'custom')
                                            <p class="text-sm text-gray-500">
                                                <strong>Specification:</strong> {{ $invoice->order->product_specification ?? 'Custom made product according to requirements' }} | 
                                                <strong>Type:</strong> Custom Product
                                            </p>
                                        @elseif ($invoice->order->product)
                                            <p class="text-sm text-gray-500">
                                                <strong>Model:</strong> {{ $invoice->order->product->model ?? '-' }} | 
                                                <strong>Wood Type:</strong> {{ $invoice->order->product->wood_type ?? '-' }} | 
                                                <strong>Details:</strong> {{ $invoice->order->product->details ?? '-' }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Quantity:</p>
                                <p class="font-semibold">{{ $invoice->order->quantity }} pcs</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Production Price:</p>
                                @if ($invoice->order->product_type === 'custom')
                                    @php
                                        $totalPembelian = $invoice->order->purchases->sum(function ($purchase) {
                                            return $purchase->quantity * $purchase->price;
                                        });
                                        $totalBiayaProduksi = $invoice->order->productionCosts->sum('amount');
                                        $totalHPP = $totalPembelian + $totalBiayaProduksi;
                                    @endphp
                                    @if ($totalHPP > 0)
                                        <p class="font-semibold text-blue-600">HPP: Rp
                                            {{ number_format($totalHPP, 0, ',', '.') }}</p>
                                        <p class="text-sm text-gray-500">+ Margin (to be determined)</p>
                                    @else
                                        <p class="font-semibold text-gray-500">Will be calculated after production is completed
                                        </p>
                                    @endif
                                @else
                                    <p class="font-semibold">Rp
                                        {{ number_format($invoice->order->total_price ?? 0, 0, ',', '.') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Invoice Details -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <!-- Discount Information -->
                    @if ($invoice->discount_amount > 0 || $invoice->discount_percentage > 0)
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-amber-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Discount Information
                        </h4>
                        <div class="space-y-2">
                            @if ($invoice->discount_percentage > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-amber-700">Discount Percentage:</span>
                                    <span class="font-semibold text-amber-800">{{ number_format($invoice->discount_percentage, 2) }}%</span>
                                </div>
                            @endif
                            @if ($invoice->discount_amount > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-amber-700">Discount Amount:</span>
                                    <span class="font-semibold text-amber-800">Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if ($invoice->discount_reason)
                                <div class="mt-2">
                                    <span class="text-sm text-amber-700">Reason:</span>
                                    <p class="text-sm text-amber-600 mt-1">{{ $invoice->discount_reason }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Shipping Information -->
                    @if ($invoice->shipping_cost > 0 || $invoice->shipping_address || $invoice->shipping_method)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-blue-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            Shipping Information
                        </h4>
                        <div class="space-y-2">
                            @if ($invoice->shipping_cost > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-blue-700">Shipping Cost:</span>
                                    <span class="font-semibold text-blue-800">Rp {{ number_format($invoice->shipping_cost, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if ($invoice->shipping_method)
                                <div>
                                    <span class="text-sm text-blue-700">Method:</span>
                                    <p class="text-sm text-blue-600">{{ $invoice->shipping_method }}</p>
                                </div>
                            @endif
                            @if ($invoice->shipping_address)
                                <div>
                                    <span class="text-sm text-blue-700">Address:</span>
                                    <p class="text-sm text-blue-600 mt-1">{{ $invoice->shipping_address }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Down Payment Information -->
                    @if ($invoice->paid_amount > 0 || $invoice->remaining_amount > 0)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h4 class="text-lg font-medium text-green-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            Payment Status
                        </h4>
                        @php
                            $totalAmount = $invoice->total_amount;
                            $paidAmount = $invoice->paid_amount ?? 0;
                            $remainingAmount = $invoice->remaining_amount ?? 0;
                        @endphp
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-green-700">Total Amount:</span>
                                <span class="font-semibold text-green-800">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                            </div>
                            @if ($paidAmount > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-green-700">Paid Amount:</span>
                                    <span class="font-semibold text-green-800">Rp {{ number_format($paidAmount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if ($remainingAmount > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-red-600">Remaining:</span>
                                    <span class="font-bold text-red-600">Rp {{ number_format($remainingAmount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if ($paidAmount > 0 && $remainingAmount <= 0)
                                <div class="mt-2 p-2 bg-green-100 rounded">
                                    <span class="text-sm font-semibold text-green-800">✓ Fully Paid</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                <hr class="my-6">

                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Cost Details</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        @if ($invoice->order->product_type === 'custom')
                            @php
                                $totalPembelian = $invoice->order->purchases->sum(function ($purchase) {
                                    return $purchase->quantity * $purchase->price;
                                });
                                $totalBiayaProduksi = $invoice->order->productionCosts->sum('amount');
                                $totalHPP = $totalPembelian + $totalBiayaProduksi;
                            @endphp
                            @if ($totalHPP > 0)
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center py-2">
                                        <span class="text-gray-700">Total Material Purchase:</span>
                                        <span class="font-semibold text-blue-600">Rp
                                            {{ number_format($totalPembelian, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2">
                                        <span class="text-gray-700">Total Production Cost:</span>
                                        <span class="font-semibold text-blue-600">Rp
                                            {{ number_format($totalBiayaProduksi, 0, ',', '.') }}</span>
                                    </div>
                                    <hr class="border-gray-300 my-2">
                                    <div class="flex justify-between items-center py-2 text-lg">
                                        <span class="font-semibold text-blue-900">Total HPP:</span>
                                        <span class="font-bold text-blue-900">Rp
                                            {{ number_format($totalHPP, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="bg-blue-100 p-3 rounded-lg">
                                        <p class="text-sm text-blue-800">
                                            <strong>Info:</strong> Selling price will be calculated from HPP + margin that will
                                            be determined after production is completed.
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="text-gray-600">Price will be calculated after production is completed</p>
                                    <p class="text-sm text-gray-500 mt-2">Based on HPP + margin</p>
                                </div>
                            @endif
                        @else
                            <div class="space-y-3">
                                <div class="flex justify-between items-center py-2">
                                    <span>Subtotal:</span>
                                    <span class="font-semibold">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                                </div>
                                
                                @if ($invoice->discount_amount > 0)
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-red-600">Discount:</span>
                                    <span class="font-semibold text-red-600">- Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</span>
                                </div>
                                @endif
                                
                                @if ($invoice->shipping_cost > 0)
                                <div class="flex justify-between items-center py-2">
                                    <span>Shipping Cost:</span>
                                    <span class="font-semibold">Rp {{ number_format($invoice->shipping_cost, 0, ',', '.') }}</span>
                                </div>
                                @endif
                                
                                <div class="flex justify-between items-center py-2">
                                    <span>Tax:</span>
                                    <span class="font-semibold">Rp {{ number_format($invoice->tax_amount ?? 0, 0, ',', '.') }}</span>
                                </div>
                                
                                <hr class="my-2">
                                <div class="flex justify-between items-center py-2 text-lg">
                                    <span class="font-semibold">Total:</span>
                                    <span class="font-bold text-green-600">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Information</h3>
                    <div class="bg-blue-50 p-4 rounded-lg">
                        @if ($invoice->order->incomes->count() > 0)
                            <div class="space-y-3">
                                @foreach ($invoice->order->incomes as $income)
                                    <div
                                        class="flex justify-between items-center py-2 border-b border-blue-200 last:border-b-0">
                                        <div>
                                            <span class="font-medium text-blue-900">{{ $income->type }}</span>
                                            <span
                                                class="text-sm text-blue-600 ml-2">({{ \Carbon\Carbon::parse($income->date)->format('d M Y') }})</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="font-semibold text-blue-900">Rp
                                                {{ number_format($income->amount, 0, ',', '.') }}</span>
                                            @if ($income->payment_method)
                                                <div class="text-xs text-blue-600">
                                                    {{ $income->payment_method_display }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                                <hr class="border-blue-200 my-3">
                                <div class="flex justify-between items-center py-2">
                                    <span class="font-semibold text-blue-900">Total Paid:</span>
                                    <span class="font-bold text-blue-900">Rp
                                        {{ number_format($invoice->order->incomes->sum('amount'), 0, ',', '.') }}</span>
                                </div>
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
                                    <div class="flex justify-between items-center py-2">
                                        <span class="font-semibold text-red-600">Remaining Payment:</span>
                                        <span class="font-bold text-red-600">Rp
                                            {{ number_format($remainingAmount, 0, ',', '.') }}</span>
                                    </div>
                                @elseif($invoice->order->product_type === 'custom' && $totalPaid > 0)
                                    <div class="flex justify-between items-center py-2">
                                        <span class="font-semibold text-green-600">Status:</span>
                                        <span class="font-bold text-green-600">Down Payment Paid (Rp
                                            {{ number_format($totalPaid, 0, ',', '.') }})</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-blue-600">No payment has been received yet</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Invoice Status -->
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Invoice Status</h3>
                        @if ($invoice->status === 'Paid')
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                                <span class="text-green-600 font-semibold text-lg">✓ PAID</span>
                            </div>
                        @else
                            <span class="text-gray-600 font-medium">{{ $invoice->status }}</span>
                        @endif
                    </div>

                    @if ($invoice->status === 'Paid')
                        <div class="mt-3 bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <p class="text-green-800 font-semibold">Invoice has been marked as PAID</p>
                                    <p class="text-green-600 text-sm">Payment has been received and processed</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Additional Notes and Terms -->
                @if ($invoice->terms_conditions || $invoice->notes_customer || $invoice->notes)
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                    <div class="grid grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if ($invoice->terms_conditions)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h4 class="text-md font-medium text-gray-800 mb-2">Terms & Conditions</h4>
                            <div class="text-sm text-gray-600 prose max-w-none">
                                {!! nl2br(e($invoice->terms_conditions)) !!}
                            </div>
                        </div>
                        @endif
                        
                        @if ($invoice->notes_customer || $invoice->notes)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h4 class="text-md font-medium text-gray-800 mb-2">Notes</h4>
                            @if ($invoice->notes_customer)
                                <div class="mb-3">
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Notes:</span>
                                    <p class="text-sm text-gray-600 mt-1">{{ $invoice->notes_customer }}</p>
                                </div>
                            @endif
                            @if ($invoice->notes)
                                <div>
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Internal Notes:</span>
                                    <p class="text-sm text-gray-600 mt-1">{{ $invoice->notes }}</p>
                                </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <div class="flex flex-wrap gap-4">
                    @if ($invoice->status !== 'Paid')
                        {{-- <form action="{{ route('invoices.updateStatus', $invoice) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="Sent">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Kirim Invoice
                            </button>
                        </form> --}}

                        <form action="{{ route('invoices.updateStatus', $invoice) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="Paid">
                            <button type="submit"
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Mark as Paid
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('invoices.download', $invoice) }}"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Download PDF
                    </a>

                    <form action="{{ route('invoices.generateAgain', $invoice) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">
                            Generate Invoice Again
                        </button>
                    </form>

                    <a href="{{ route('orders.show', $invoice->order) }}"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Back to Order
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
