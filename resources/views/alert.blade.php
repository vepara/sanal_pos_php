<div class="image-container set-full-height">
    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
                <div class="wizard-container">
                    <div class="card wizard-card">

                        @if (Session::get('email_sent')===true)
                            <div class="icon-circle" style="border: 3px solid green; margin-top:10px">
                                <i class="ti-check" style="color: green"></i>
                            </div>
                            <h4 class="info-text"> Tebrikler!</h4>
                            <h5 class="text-center"> Email başarılı bir şekilde gönderildi.</h5>
                        @else
                            <div class="icon-circle" style="border: 3px solid red; margin-top:10px">
                                <i class="ti-check" style="color: red"></i>
                            </div>
                            <h4 class="info-text"> Hata!</h4>
                            <h5 class="text-center">Email gönderilirken bir hata oluştu.</h5>
                        @endif
                        <br />
                            <div id="loading" class="text-center" style="display: none;">
                                <div class="spinner-border" role="status">
                                </div>
                                <p>Anasayfa'ya yönlendiriliyorsunuz..</p>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var duration = 5000;
        function showLoadingAndRedirect() {
            document.getElementById('loading').style.display = 'block';
            setTimeout(redirectToHomePage, duration);
        }
        function redirectToHomePage() {
            window.location.href = "{{ route('index') }}";
        }
        showLoadingAndRedirect();
    });
</script>
