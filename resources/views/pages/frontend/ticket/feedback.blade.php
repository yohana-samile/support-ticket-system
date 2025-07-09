@extends('layouts.frontend.app')
@section('title', 'Ticket feedback')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Ticket Feedback: #{{ $ticket->ticket_number }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('frontend.ticket.submit-feedback', $ticket->uid) }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <h6>Was your issue resolved satisfactorily?</h6>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="satisfaction" id="satisfied-yes" value="1" required>
                                    <label class="form-check-label" for="satisfied-yes">
                                        Yes, my issue was resolved
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="satisfaction" id="satisfied-no" value="0">
                                    <label class="form-check-label" for="satisfied-no">
                                        No, I still need help
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="comments" class="form-label">Additional comments</label>
                                <textarea class="form-control" id="comments" name="comments" rows="4" placeholder="Optional feedback about your experience"></textarea>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-primary">
                                    Submit Feedback
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
