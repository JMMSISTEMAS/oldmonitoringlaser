<?php
    $usuarios = [
        ['Admin', '#rendilaser2022$'],
        ['Granada', '#20lasegran22$'],
        ['Madrid', '#2022capital$'],
        ['Levante', '#2022este2022$'],
        ['Noreste', '#lasernorte2022$'],
    ];  

    $usuario = $usuarios[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    
    <!-- scripts de terceros -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.min.js" integrity="sha384-ODmDIVzN+pFdexxHEHFBQH3/9/vQ9uori45z4JjnFsRydbmQbmL5t1tQ0culUzyK" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito&family=Roboto&display=swap" rel="stylesheet">

    <!-- scripts propios -->
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="styles/login.css">

    <title>Acceso identificado</title>
</head>
<body>
    <h1>Acceso identificado</h1>
    <form method="post" action="server/services/login.php">
        <div class="mb-3">
            <label for="exampleInputEmail1" class="form-label">Usuario:</label>
            <input class="form-control" name="usuario" required value="<?php echo $usuario[0]?>">
        </div>
        <div class="mb-3">
            <label for="exampleInputPassword1" class="form-label">Contraseña:</label>
            <input type="password" class="form-control" required name="pass" value="<?php echo $usuario[1]?>">
        </div>
        <div class="alert alert-danger datos_incorrectos" id="datos_incorrectos">
            Datos incorrectos
        </div>
        <button type="submit" class="btn btn-success">Entrar</button>
    </form>
</body>

<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        const get_param = window.location.search.substr(1)
        if(get_param == "pass=false"){
            const error_msg = document.getElementById("datos_incorrectos")
            error_msg.style.display = "block";
        }
    })
</script>

</html>