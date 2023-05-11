@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12 text-danger">
            <h3>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                    class="bi bi-exclamation-diamond" viewBox="0 0 16 16">
                    <path
                        d="M6.95.435c.58-.58 1.52-.58 2.1 0l6.515 6.516c.58.58.58 1.519 0 2.098L9.05 15.565c-.58.58-1.519.58-2.098 0L.435 9.05a1.482 1.482 0 0 1 0-2.098L6.95.435zm1.4.7a.495.495 0 0 0-.7 0L1.134 7.65a.495.495 0 0 0 0 .7l6.516 6.516a.495.495 0 0 0 .7 0l6.516-6.516a.495.495 0 0 0 0-.7L8.35 1.134z" />
                    <path
                        d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z" />
                </svg>
                Document Tracking System Verification Notice</h3>
                <div class="alert alert-info">
                    We need you to update some information regarding your account. Please provide the following in order to proceed.
                </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                <label>Enter Cats #</label>
                <input type="text" class="form-control mb-2 col-2" id="cats" />
                <button type="button" class="btn btn-primary" id="verify">
                    Check then Verify
                </button>
            </div>
        </div>
        {{-- <div class="col-12">
            <div class="form-group">
                <h3>Is this you?</h3>
                <span class="fullname"></span>
            </div>
        </div> --}}
    </div>

@endsection

@section('scripts')
    <script>
        $(function() {
            $('#verify').click(function() {
                loading()
                let cats = $('#cats').val();
                axios.get(`http://ems.dvodeoro.ph/employeesData?cats=${cats}`).then(response => {
                    
                    var fullname = response.data.first_name + " " + response.data.last_name
                    
                    let text = "You are " + fullname + "? If YES, press OK then verify. If NO, Press cancel and contact PICTO to verify.";
                    if (confirm(text) == true) {
                        let text = "By clicking OK you are confirming that the information that provided is true."
                        if (confirm(text) == true) {
                            axios.post('confirm-verification', { 
                                cats: cats,
                                fullname: fullname
                            }).then(response=> {
                                location.href = "/"
                                alert('Account is verified and updated.')
                            })
                        } else {
                            alert('Verification cancelled')
                        }
                    } else {
                         alert('Verification cancelled')
                    }
                    unloading()
                }).catch( error => {
                    alert(error.response.data)
                    unloading()
                })
            })

            function loading() {
                var loading =
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="false"></span>'

                $('#verify').append(loading)
                $('#verify').attr('disabled', 'disabled')
            }

            function unloading() {
                $('#verify').empty()
                $('#verify').text("Check then Verify")
                $('#verify').removeAttr('disabled')
            }
        })
    </script>
@endsection
