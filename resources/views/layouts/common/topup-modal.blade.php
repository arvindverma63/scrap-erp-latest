@php use App\Models\User; @endphp
        <!-- Top Up Modal -->
<div class="modal fade" id="topUpModal" tabindex="-1" aria-labelledby="topUpModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.wallets.requestTopup') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="topUpModalLabel">Top Up Balance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                @if(auth()->user()->roles()->first()->name == 'super-admin' || auth()->user()->roles()->first()->name == 'admin')
                    @php
                        $cashiers = User::role('cashier')->select(['id', 'name'])->get();
                    @endphp
                    <div class="modal-body">
                        <label for="amount" class="form-label">Cashier</label>
                        <select class="form-control select2-cashier" name="cashier_id" required>
                            <option value="">Select Cashier</option>
                            @foreach($cashiers as $cashier)
                                <option value="{{$cashier->id}}">{{$cashier->name}}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="modal-body">
                    <label for="amount" class="form-label">Amount (JMD)</label>
                    <input type="number" class="form-control" name="amount" id="amount" min="1"
                           required>
                </div>
                @if(auth()->user()->roles()->first()->name != 'super-admin' && auth()->user()->roles()->first()->name != 'admin')
                    <div class="modal-body">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" name="remarks" id="remarks" placeholder="Write your remarks"
                                  required></textarea>
                    </div>
                @endif
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Confirm Top Up</button>
                </div>
            </div>
        </form>

    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.1/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $(container).find('.select2-cashier').select2({
            placeholder: "-- Select Cashier --",
            allowClear: true,
            width: '100%',
        });
    });
</script>