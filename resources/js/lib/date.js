function formatDate(date) {
  const year = date.getFullYear().toString().slice(-2); // Obtener los últimos 2 dígitos del año
  const month = String(date.getMonth() + 1).padStart(2, '0'); // Mes (1-12), con 2 dígitos
  const day = String(date.getDate()).padStart(2, '0'); // Día (1-31), con 2 dígitos

  const hours = String(date.getHours()).padStart(2, '0'); // Hora (0-23), con 2 dígitos
  const minutes = String(date.getMinutes()).padStart(2, '0'); // Minutos (0-59), con 2 dígitos
  const seconds = String(date.getSeconds()).padStart(2, '0'); // Segundos (0-59), con 2 dígitos

  return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

export { formatDate }