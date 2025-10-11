@extends('layouts.app')

@section('title', 'Dashboard - ' . config('app.name'))

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Welcome back, {{ Auth::user()->name }}!</h1>
                <span class="badge bg-primary fs-6">Dashboard</span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Quick Stats -->
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">Posts</h4>
                            <h2 class="mb-0">0</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-newspaper fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">Views</h4>
                            <h2 class="mb-0">0</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-eye fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">Comments</h4>
                            <h2 class="mb-0">0</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-comments fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">Drafts</h4>
                            <h2 class="mb-0">0</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-edit fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="d-grid">
                                <button class="btn btn-primary btn-lg">
                                    <i class="fas fa-plus me-2"></i>
                                    Create New Post
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-grid">
                                <button class="btn btn-secondary btn-lg">
                                    <i class="fas fa-list me-2"></i>
                                    View All Posts
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-grid">
                                <button class="btn btn-info btn-lg">
                                    <i class="fas fa-user-edit me-2"></i>
                                    Edit Profile
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Activity</h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-4">
                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No recent activity</h5>
                        <p class="text-muted">Start creating content to see your activity here.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

