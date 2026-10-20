let usuarios = [];


function crearCuenta(){
    try{
        let name = document.getElementById("nombre").value.trim();
        let apell = document.getElementById("apellidos").value.trim();                   
        let email = document.getElementById("correo").value.trim();              
        let pass = document.getElementById("contrasena").value.trim();  
        let reptirPass = document.getElementById("confirmar").value.trim();
        let depart = document.getElementById("departamento").value; 

        // comprobar que sean iguales las contraseñas
        if (pass !== reptirPass) {
            document.getElementById("contrasena").value=" ";
            document.getElementById("confirmar").value=" ";
            throw new Error("Las contraseñas no coinciden");
        }
        if (validarName(name) && validarApell(apell) && validarPass(pass) && validarRepetirPass(reptirPass)){
            let encontrado = false;
            for(let i = 0; i < usuarios.length && !encontrado; i ++){
                
                if (usuarios[i].nombre == name && usuarios[i].apellidos == apell && usuarios[i].correo == email) {
                    encontrado = true;
                }
            }

            if(encontrado){
                throw new Error("Usuario ya esta registrado")
            }else{
                let nuevoUsuario = {
                    nombre: name,
                    apellido: apell,
                    correro: email,
                    contraseña: pass,
                    departamento: depart
                };

                usuarios.push(nuevoUsuario);

                localStorage.setItem("usuario", JSON.stringify(usuarios));

                fetch("index.php?controller=registrar&action=registrar", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(nuevoUsuario)
                })
            }
        }

    }catch(e){
        alert(e.message);
    }
}



function validarName(name) {
    let expreg = new RegExp("^[A-Za-zÁÉÍÓÚáéíóú]+$");
    let expregCorrecta = false;
    if(expreg.test(name)){
        expregCorrecta = true;
    }else{
        document.getElementById("nombre").value = "";
    }
    return expregCorrecta;

}

function validarApell(apell){
    let expreg = new RegExp("^[A-Za-zÁÉÍÓÚáéíóú]+$");
    let expregCorrecta = false;
    if(expreg.test(apell)){
        expregCorrecta = true;
    }else{
        document.getElementById("apellidos").value = "";
    }
    return expregCorrecta;


}

function validarPass(pass){
    let expreg = new RegExp("^[A-Za-z0-9@$!%*?&#_\\-]+$");
    let expregCorrecta = false;
    if(expreg.test(pass)){
        expregCorrecta = true;
    }else{
        document.getElementById("contrasena").value = "";
    }
    return expregCorrecta;


}

function validarRepetirPass(reptirPass) {
    let expreg = new RegExp("^[A-Za-z0-9]+$");
    let expregCorrecta = false;
    if(expreg.test(reptirPass)){
        expregCorrecta = true;
    }else{
        document.getElementById("confirmar").value = "";
    }
    return expregCorrecta;


}


document.getElementById("btnCrearCuenta").addEventListener("submit", crearCuenta);
