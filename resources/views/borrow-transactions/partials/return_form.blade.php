<form action="{{ route('borrow-transactions.update', $borrowTransaction) }}" method="POST" class="space-y-6">
    @csrf
    @method('PATCH')

    <div>
        <h4 class="text-lg font-black tracking-tight text-white mb-1">Confirm Return</h4>
        <p class="text-[10px] font-bold opacity-40 uppercase tracking-widest">Processing record for {{ $borrowTransaction->student->name }}</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-error text-xs rounded-xl">{{ $errors->first() }}</div>
    @endif

    <div class="space-y-4">
        <div class="form-control">
            <label class="label">
                <span class="label-text font-black text-[10px] uppercase tracking-[0.2em] text-white/40">Books to Return</span>
            </label>
            <div class="relative">
                <input
                    type="number"
                    name="quantity_returned"
                    class="input w-full bg-white/5 border-white/10 focus:border-primary/50 focus:ring-4 focus:ring-primary/10 rounded-xl h-12 text-white transition-all font-bold text-center text-xl"
                    min="1"
                    max="{{ $borrowTransaction->quantity_borrowed - $borrowTransaction->quantity_returned }}"
                    value="{{ old('quantity_returned', $borrowTransaction->quantity_borrowed - $borrowTransaction->quantity_returned) }}"
                    required
                >
                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none opacity-40 text-[10px] font-bold uppercase tracking-widest">
                    of {{ $borrowTransaction->quantity_borrowed - $borrowTransaction->quantity_returned }} cop(ies)
                </div>
            </div>
        </div>

        <div class="bg-white/5 rounded-2xl p-5 border border-white/10 space-y-3">
            <h5 class="text-[9px] font-black uppercase tracking-[0.2em] text-white/40">Financial Summary</h5>
            <div class="flex justify-between items-center">
                <span class="text-xs opacity-60">Fine Policy</span>
                <span class="text-xs font-bold text-white/80">₱10.00 / day / book</span>
            </div>
            <div class="divider m-0 opacity-10"></div>
            @if ($borrowTransaction->due_date < now())
                @php $overdueDays = (int) now()->diffInDays($borrowTransaction->due_date); @endphp
                <div class="flex justify-between items-center text-error">
                    <span class="text-xs font-bold">Status</span>
                    <span class="text-xs font-black uppercase tracking-widest">{{ $overdueDays }} Day(s) Overdue</span>
                </div>
                <div class="flex justify-between items-center mt-2 p-3 bg-error/10 rounded-xl border border-error/20">
                    <span class="text-xs font-bold text-error">Collect Fine:</span>
                    <span class="text-xl font-black text-error">₱{{ number_format($borrowTransaction->fine_amount, 2) }}</span>
                </div>
            @else
                <div class="flex justify-between items-center text-success">
                    <span class="text-xs font-bold">Status</span>
                    <span class="text-xs font-black uppercase tracking-widest italic text-[10px]">On Schedule</span>
                </div>
                <div class="text-[9px] text-white/40 italic text-center">No fine applicable for this return</div>
            @endif
        </div>
    </div>

    <div class="modal-action border-t border-white/10 pt-6 mt-6">
        <button type="button" @click="showReturnModal = false" class="btn btn-ghost rounded-xl px-8 text-white/40 hover:text-white hover:bg-white/5 transition-all">Cancel</button>
        <button type="submit" class="btn border-none bg-gradient-to-r from-primary to-primary-focus hover:scale-105 active:scale-95 text-white font-black uppercase tracking-widest text-[10px] rounded-xl px-12 h-12 shadow-xl shadow-primary/20 transition-all duration-300">
            Confirm & Save
        </button>
    </div>
</form>
