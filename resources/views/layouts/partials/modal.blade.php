<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-primary" type="submit">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Sticker Note Modal -->
<div class="modal fade" id="stickerModal" tabindex="-1" role="dialog" aria-labelledby="stickerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="stickerModalLabel">Add Sticker Note</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            @include('layouts.partials._sticker_form')
        </div>
    </div>
</div>

<script>
    ClassicEditor
        .create(document.querySelector('#stickerNote'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' }
                ]
            }
        })
        .catch(error => {
            console.error(error);
        });

    document.getElementById('privateNote').addEventListener('change', function() {
        const recipientGroup = document.getElementById('recipientGroup');
        const recipientSelect = document.getElementById('recipient');

        if (this.checked) {
            // If private is checked, hide the recipient group and reset the select
            recipientGroup.style.display = 'none';
            recipientSelect.value = ''; // Clear the selected value
        } else {
            // If private is unchecked, show the recipient group
            recipientGroup.style.display = 'block';
        }
    });
</script>
