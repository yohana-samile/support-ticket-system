@extends('layouts.frontend.app')
@section('title', 'Share Your Feedback')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-white border-0 pt-4">
                        <div class="text-center">
                            <h2 class="h4 text-primary mb-2">How was your experience?</h2>
                            <p class="text-muted">Help us improve by sharing your feedback for ticket #{{ $ticket->ticket_number }}</p>
                        </div>
                    </div>

                    <div class="card-body px-5 py-4">
                        <form action="{{ route('frontend.ticket.submit-feedback', $ticket->uid) }}" method="POST">
                            @csrf

                            <!-- Satisfaction Rating -->
                            <div class="mb-5 text-center">
                                <h5 class="mb-4">Was your issue resolved to your satisfaction?</h5>
                                <div class="d-flex justify-content-center">
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <label class="btn btn-outline-success px-4 py-2 rounded-start">
                                            <input type="radio" name="satisfaction" value="1" required>
                                            <i class="far fa-smile fa-lg me-2"></i> Yes
                                        </label>
                                        <label class="btn btn-outline-danger px-4 py-2 rounded-end">
                                            <input type="radio" name="satisfaction" value="0">
                                            <i class="far fa-frown fa-lg me-2"></i> No
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Comments -->
                            <div class="mb-4">
                                <label for="comments" class="form-label fw-bold">
                                    <i class="far fa-comment-dots me-2"></i> Additional comments
                                </label>
                                <textarea class="form-control border-2" id="comments" name="comments" rows="4"
                                          placeholder="What could we do better? (Optional)"
                                          style="border-color: #e0e0e0;"></textarea>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-center mt-5">
                                <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill">
                                    <i class="far fa-paper-plane me-2"></i> Submit Feedback
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="card-footer bg-white border-0 text-center pb-4">
                        <small class="text-muted">
                            <i class="fa fa-lock me-1"></i> Your feedback is confidential
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .btn-group-toggle .btn {
                transition: all 0.3s ease;
            }
            .btn-group-toggle .btn:hover {
                transform: translateY(-2px);
            }
            .btn-group-toggle .active {
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            }
            .form-control:focus {
                border-color: #4e73df;
                box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.1);
            }
            .card {
                border-radius: 12px;
                overflow: hidden;
            }
        </style>
    @endpush
@endsection
