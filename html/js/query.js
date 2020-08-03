const form = document.querySelector("form[name=generator]");
const $methodSelect = form.querySelector("select[name=method]");
const $hostInput = form.querySelector("input[name=host]");
const $formatSelect = form.querySelector("select[name=format]");
const $fieldSelect = form.querySelector("select[name=field]");
const $submitArea = document.querySelector("span#submit");

$formatSelect.addEventListener("change", () => {
  const format = $formatSelect.value;
  $fieldSelect.disabled = format === "html";
  if ($fieldSelect.disabled) $fieldSelect.value = "";
});

form.addEventListener("submit", (e) => e.preventDefault());

function generateUrlPath(params) {
  return params.join("/");
}

function generateQueryPath(params) {
  return Object.keys(params).length === 0
    ? ""
    : `?${Object.entries(params)
        .map(([k, v]) => `${k}=${v}`)
        .join("&")}`;
}

function generateLink(uri) {
  const { protocol, host } = window.location;
  const link = `${protocol}//${host}/${uri}`;
  return `<a href="${link}" target="_blank">${link}</a>`;
}

function generateForm(params) {
  const { protocol, host } = window.location;
  return `<form method="post" action="${protocol}//${host}/">
    ${Object.entries(params)
      .map(([k, v]) => `<input type="hidden" name="${k}" value="${v}">`)
      .join("")}
    <input type="submit" value="Submit">
  </form>`;
}

function generateQuery() {
  const params = Object.fromEntries(
    Object.entries({
      host: $hostInput.value,
      format: $formatSelect.value,
      field: $fieldSelect.value,
    }).filter(([, v]) => !!v)
  );
  let html;
  switch ($methodSelect.value) {
    default:
    case "url":
      html = generateLink(generateUrlPath(Object.values(params)));
      break;
    case "get":
      html = generateLink(generateQueryPath(params));
      break;
    case "post":
      html = generateForm(params);
      break;
  }
  $submitArea.innerHTML = html;
}

[$hostInput, $methodSelect, $formatSelect, $fieldSelect].forEach((f) =>
  f.addEventListener("change", generateQuery)
);
generateQuery();
