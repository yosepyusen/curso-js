import saludar, {Saludar, PI, pass, usuario} from "./constantes.js" //busca el archivo js con ese nombre
//import {multiplicar, aritmetica} from "./aritmetica.js"
import {multiplicar, aritmetica as calc} from "./aritmetica.js" //asigfnamos una alias a artimetica
//primerpo se exporta y despues aqui improtamos las variables
console.log("Archivo modulo.js");

console.log(PI,pass, usuario);
console.log(multiplicar(5,5));
//console.log(aritmetica.restar(8,12));
//console.log(aritmetica.sumar(8,12));
console.log(calc.restar(8,12)); //aqui llamammos por el alias
console.log(calc.sumar(8,12));
saludar();
let saludo = new Saludar();
saludo;

