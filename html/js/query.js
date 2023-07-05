const form = document.querySelector("form[name=generator]");
const $methodSelect = form.querySelector("select[name=method]");
const $versionSelect = form.querySelector("select[name=version]");
const $queryInput = form.querySelector("input[name=q]");
const $formatSelect = form.querySelector("select[name=format]");
const $fieldSelect = form.querySelector("select[name=field]");
const $submitArea = document.querySelector("span#submit");

$formatSelect.addEventListener("change", () => {
  const [format, pretty] = $formatSelect.value.split('-');
  $fieldSelect.disabled = format === "html";
  if ($fieldSelect.disabled) $fieldSelect.value = "";
});

form.addEventListener("submit", (e) => e.preventDefault());

function generateUrlPath(params, pretty) {
  return `${params.join("/")}${pretty ? '?pretty' : ''}`;
}

function generateQueryPath(params, pretty) {
  return Object.keys(params).length === 0
    ? ""
    : `?${Object.entries(params)
      .map(([k, v]) => `${k}=${v}`)
      .join("&")}${pretty ? '&pretty' : ''}`;
}

function generateLink(uri) {
  const { protocol, host } = window.location;
  const link = `${protocol}//${host}/${uri}`;
  return `<a href="${link}" target="_blank">${link}</a>`;
}

function generateForm(params, pretty) {
  const { protocol, host } = window.location;
  return `<form method="post" action="${protocol}//${host}/">
    ${Object.entries(params)
      .map(([k, v]) => `<input type="hidden" name="${k}" value="${v}">`)
      .join("")}
    ${pretty ? '<input type="hidden" name="pretty" value="">' : ''}
    <input type="submit" value="Submit">
  </form>`;
}

function generateQuery() {
  const [format, pretty] = $formatSelect.value.split('-');
  const params = Object.fromEntries(
    Object.entries({
      q: $queryInput.value,
      version: $versionSelect.value,
      format,
      field: $fieldSelect.value,
    }).filter(([, v]) => !!v)
  );
  let html;
  switch ($methodSelect.value) {
    default:
    case "url":
      html = generateLink(generateUrlPath(Object.values(params), pretty));
      break;
    case "get":
      html = generateLink(generateQueryPath(params, pretty));
      break;
    case "post":
      html = generateForm(params, pretty);
      break;
  }
  $submitArea.innerHTML = html;
}

[$methodSelect, $queryInput, $versionSelect, $formatSelect, $fieldSelect].forEach((f) =>
  f.addEventListener("change", generateQuery)
);
generateQuery();
