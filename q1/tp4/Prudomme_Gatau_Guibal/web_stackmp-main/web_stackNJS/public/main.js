async function callPhp(url, resultId) {
  const result = document.getElementById(resultId);
  result.innerHTML = '<p>🔄 Chargement...</p>';
  try {
    const response = await fetch(url);
    const html = await response.text();
    result.innerHTML = html;
  } catch (error) {
    result.innerHTML = `<p style="color:red">❌ Erreur: ${error.message}</p>`;
    console.error('AJAX error:', error);
  }
}
