<!doctype html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
</head>

<body class="h-100 d-flex justify-content-center align-items-center bg-dark">
    <div class="card">
        <div class="card-body">
            <form id="formLogin">
                <div class="mb-3">
                    <label for="inputUser" class="form-label">Username</label>
                    <div class="input-group mb-3" id="inputUser">
                        <input type="text" class="form-control" placeholder="Username" aria-label="Username" name="user">
                        <span class="input-group-text">@</span>
                        <input type="text" class="form-control" placeholder="Server" aria-label="Server" value="localhost" name="host">
                        <input type="number" class="form-control" placeholder="Port" min="1" max="65535" aria-label="Port" value="22" name="port">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label">Password</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" name="pass">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $("#formLogin").submit(function(event) {
                var values = {};
                $.each($('#formLogin').serializeArray(), function(i, field) {
                    values[field.name] = field.value;
                });

                $.post("api/", {
                        action: "testLogin",
                        host: values.host,
                        port: values.port,
                        user: values.user,
                        pass: values.pass
                    })
                    .done(function(data) {
                        if (data['msg'] == "success") {
                            window.location.replace("./");
                        } else {
                            Swal.fire({
                                icon: 'Error',
                                title: 'Fail!',
                                text: 'Wrong user or password.',
                                timer: 2000
                            })
                        }
                    });

                event.preventDefault();
            });
        });
    </script>
</body>

</html>