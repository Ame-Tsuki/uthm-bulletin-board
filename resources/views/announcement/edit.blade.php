@extends('layouts.app')

@section('title', 'Edit Announcement')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Edit Announcement</h4>
                        <a href="{{ route('announcements.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Announcements
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('announcements.update', $announcement) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> 
                                    <strong>Created:</strong> {{ $announcement->created_at->format('F d, Y H:i') }}
                                    @if($announcement->user)
                                        by {{ $announcement->user->name }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="title" class="form-label fw-bold">Title *</label>
                                <input type="text" 
                                       class="form-control @error('title') is-invalid @enderror" 
                                       id="title" 
                                       name="title" 
                                       value="{{ old('title', $announcement->title) }}" 
                                       placeholder="Enter announcement title" 
                                       required>
                                @error('title')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="content" class="form-label fw-bold">Content *</label>
                                <textarea class="form-control @error('content') is-invalid @enderror" 
                                          id="content" 
                                          name="content" 
                                          rows="10" 
                                          placeholder="Enter announcement content" 
                                          required>{{ old('content', $announcement->content) }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <small class="text-muted">You can use HTML formatting in the content.</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label fw-bold">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" 
                                        name="status">
                                    <option value="draft" {{ old('status', $announcement->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status', $announcement->status) == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="archived" {{ old('status', $announcement->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="publish_date" class="form-label fw-bold">Publish Date</label>
                                <input type="datetime-local" 
                                       class="form-control @error('publish_date') is-invalid @enderror" 
                                       id="publish_date" 
                                       name="publish_date" 
                                       value="{{ old('publish_date', $announcement->publish_date ? $announcement->publish_date->format('Y-m-d\TH:i') : '') }}">
                                @error('publish_date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <small class="text-muted">Leave empty to publish immediately</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="expiry_date" class="form-label fw-bold">Expiry Date</label>
                                <input type="datetime-local" 
                                       class="form-control @error('expiry_date') is-invalid @enderror" 
                                       id="expiry_date" 
                                       name="expiry_date" 
                                       value="{{ old('expiry_date', $announcement->expiry_date ? $announcement->expiry_date->format('Y-m-d\TH:i') : '') }}">
                                @error('expiry_date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <small class="text-muted">Optional - when this announcement should expire</small>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="#" 
                                           class="btn btn-outline-danger" 
                                           onclick="event.preventDefault(); document.getElementById('delete-form').submit();">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                    <div>
                                        <a href="{{ route('announcements.show', $announcement) }}" class="btn btn-info">
                                            <i class="fas fa-eye"></i> Preview
                                        </a>
                                        <a href="{{ route('announcements.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update Announcement
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Delete Form -->
                    <form id="delete-form" action="{{ route('announcements.destroy', $announcement) }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 10px;
    }
    
    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
    
    .form-label {
        color: #495057;
    }
    
    .btn {
        border-radius: 5px;
        padding: 8px 20px;
    }
    
    .alert-info {
        background-color: #e7f3ff;
        border-color: #b6d4fe;
        color: #0c5460;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set min date for expiry date based on publish date
        const publishDateInput = document.getElementById('publish_date');
        const expiryDateInput = document.getElementById('expiry_date');
        
        if (publishDateInput.value) {
            expiryDateInput.min = publishDateInput.value;
        }
        
        publishDateInput.addEventListener('change', function() {
            if (this.value) {
                expiryDateInput.min = this.value;
            }
        });
        
        // Confirm before deleting
        const deleteForm = document.getElementById('delete-form');
        const deleteButton = document.querySelector('.btn-outline-danger');
        
        if (deleteButton) {
            deleteButton.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this announcement? This action cannot be undone.')) {
                    deleteForm.submit();
                }
            });
        }
        
        // Add confirmation when changing status to published
        const statusSelect = document.getElementById('status');
        const form = document.querySelector('form');
        
        form.addEventListener('submit', function(e) {
            if (statusSelect.value === 'published' && '{{ $announcement->status }}' !== 'published') {
                if (!confirm('Are you sure you want to publish this announcement? It will be visible to users.')) {
                    e.preventDefault();
                }
            }
        });
        
        // Rich text editor (optional - you can use a proper editor like TinyMCE or CKEditor)
        // Uncomment if you want to add a rich text editor
        /*
        if (typeof ClassicEditor !== 'undefined') {
            ClassicEditor
                .create(document.querySelector('#content'))
                .catch(error => {
                    console.error(error);
                });
        }
        */
    });
</script>
@endpush
@endsection