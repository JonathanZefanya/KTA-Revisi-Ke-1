@php($user = auth()->user())
@extends('layouts.user')
@section('title','Profile')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h5 fw-semibold mb-0">Profile</h1>
    </div>

    <div class="surface p-3 small mb-3">
        <div class="fw-semibold mb-2">Informasi Akun</div>

        <div class="mb-3">
            <label class="form-label">Nama</label>
            <div class="form-control">{{ $user->name }}</div>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <div class="form-control">{{ $user->email }}</div>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <br/> 
            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                Ubah Password
            </button>
        </div>
    </div>

    {{-- MODAL --}}
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ubah Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>


                <form method="POST" action="{{ route('profile.updatePassword') }}">
                    @csrf

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">Password Lama</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="new_password_confirmation" class="form-control" required>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection
