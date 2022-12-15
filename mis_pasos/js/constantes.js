export const PI= Math.PI //export siempre para manda llamar sino da error

export let usuario = "Jon";
export let pass ="qwerty";
//---------podemos ecportar con cosntantres-------
//export default pass;

//funcion expresada
const hello =() => console.log("Esto es una funcion expresada");

//funbcion definida
export default function saludar(){ // cuando se mande llamr esta funciuon se export automaticamente
    //solo una vez se hace el default, se da para CLASES Y
    console.log("Hola nodsulo egma srcitp")
}

export  class Saludar{
    constructor(){
        console.log("Hola clases +ES6");
    }
}