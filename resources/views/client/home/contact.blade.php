<div>
    <a class="btn btn-danger" href="#" data-bs-toggle="modal" data-bs-target="#contactModal">Contact Us</a>
    <div class="modal fade text-body text-start" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title fs-5" id="contactModalLabel">Contact Us</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('contact') }}" method="post">
                        @csrf
                        @honeypot

                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" maxlength="50" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="surname" class="form-label">
                                Surname <span class="text-danger">*</span>
                            </label>
                            <input type="text" maxlength="50" class="form-control @error('surname') is-invalid @enderror"
                                   id="surname" name="surname" value="{{ old('surname') }}" required>
                            @error('surname')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">
                                Phone
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">+993</span>
                                <input type="number" min="60000000" max="71999999" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone') }}">
                            </div>
                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                E-mail
                            </label>
                            <input type="email" maxlength="50" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">
                                Message <span class="text-danger">*</span>
                            </label>
                            <textarea maxlength="255" rows="3" class="form-control @error('message') is-invalid @enderror"
                                      id="message" name="message" required>{{ old('message') }}</textarea>
                            @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi-send"></i> Send
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
