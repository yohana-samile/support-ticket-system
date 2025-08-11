<script>
    document.addEventListener('DOMContentLoaded', function() {
        $(document).ready(function() {
            @if(Session::has('success'))
                toastr.success('{{ Session::get('success') }}', 'Success', {
                    closeButton: true,
                    progressBar: true,
                    timeOut: 5000
                });
            @endif

            @if(Session::has('error'))
                toastr.error('{{ Session::get('error') }}', 'Error', {
                    closeButton: true,
                    progressBar: true,
                    timeOut: 10000
                });
            @endif

            @if($errors->any())
                @foreach($errors->all() as $error)
                    toastr.error('{{ $error }}', 'Validation Error', {
                        closeButton: true,
                        progressBar: true,
                        timeOut: 10000
                    });
                @endforeach
            @endif
        });
    });
</script>
