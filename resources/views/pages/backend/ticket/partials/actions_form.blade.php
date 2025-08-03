<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-light text-dark">
                <i class="fas fa-sync-alt mr-1"></i> {{ __('label.update_status') }}
            </div>
            <div class="card-body">
                {{-- Update Status --}}
                <form id="updateStatusForm" action="{{ route('backend.ticket.update-status', $ticket->uid) }}" method="POST" class="mb-3">
                    @csrf
                    <div class="form-group">
                        <label for="status">{{__('label.update_status')}}</label>
                        <select class="form-control" id="status" name="status" required>
                            <option>{{__('label.select_status')}}</option>
                            @foreach($statuses  as $status)
                                <option value="{{ $status->name }}" {{ $ticket->status === $status->name ? 'selected' : '' }}>{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Progress bar container -->
                    <div class="progress mb-3 d-none" id="statusProgressBar">
                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                             role="progressbar"
                             style="width: 0%"></div>
                    </div>

                    <button type="submit" class="btn btn-success" id="statusSubmitBtn">
                        <i class="fas fa-check-circle mr-1"></i> {{__('label.update_status')}}
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if(!in_array($ticket->status, ['resolved', 'closed']))
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light text-dark">
                    <i class="fas fa-user-edit mr-1"></i> {{ __('label.reassign_to') }}
                </div>
                <div class="card-body">
                    {{-- Reassign User --}}
                    <form id="reassignForm" action="{{ route('backend.ticket.reassign', $ticket->uid) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="assigned_to">{{__('label.reassign_to')}}</label>
                            <select class="form-control" id="assigned_to" name="assigned_to">
                                <option selected hidden disabled>{{__('label.select_user')}}</option>
                            </select>
                        </div>

                        <!-- Progress bar container -->
                        <div class="progress mb-3 d-none" id="reassignProgressBar">
                            <div class="progress-bar progress-bar-striped progress-bar-animated"
                                 role="progressbar"
                                 style="width: 0%"></div>
                        </div>

                        <button type="submit" class="btn btn-primary" id="reassignSubmitBtn">
                            <i class="fas fa-user-edit mr-1"></i> {{__('label.reassign')}}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
