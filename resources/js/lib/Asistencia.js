import { formatDate } from '@/lib/date';

function formatDateTime(date) {
  if (!date) return null;
  return new Intl.DateTimeFormat('es', {
  year: 'numeric',
  month: 'short',
  day: 'numeric' ,
  hour: '2-digit',
  minute: '2-digit',
  }).format(new Date(date));
}

function formatTime(date) {
  let hours = date.getHours();
  let minutes = date.getMinutes();

  // Asegurarte de que las horas y minutos tengan dos dígitos
  hours = hours < 10 ? '0' + hours : hours;
  minutes = minutes < 10 ? '0' + minutes : minutes;

  return `${hours}:${minutes}`;
}

class Asistencia {
  constructor(props){ 
    this.data = props
    this.fecha = formatDateTime(new Date(props.hora_entrada)).slice(0,11),
    this.check = true
    this.entrada = new Date(props.hora_entrada)
    this.salida = props.hora_salida == null ? null : new Date(props.hora_salida);

    this.type = this.entrada.getHours() < 12 ? 'A': this.entrada.getHours() < 18 ? 'B' : 'C';
  
    // Props
    this.id = props.id
    this.nombres = `${props.nombres} ${props.apellidos}`
    this.dni = props.dni
    this.rol = props.rol
    this.hora_entrada = formatTime(this.entrada)
    this.hora_salida = this.salida == null ? null : formatTime(this.salida)
    this.fecha_entrada = formatDateTime(new Date(props.hora_entrada))
    this.fecha_salida = this.salida == null ? null : formatDateTime(new Date(props.hora_salida))
  }
  getFecha() {
    return 'jjjjj';
  }
  getHourStart() {
    return this.getTime(this.entrada)
  }

  getHourEnd() {

  }
  getTime(date) {
    const h = date.getHours().toString()
    const m = date.getMinutes().toString()
    console.log(h,m)
    return `${h.length < 2 ? '0' : ''}${h}:${m.length < 2 ? '0' : ''}${m}`
  }
  getHoraSalida(h) {

  }
}

export default Asistencia