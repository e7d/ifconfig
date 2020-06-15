const $form = document.querySelector("form[name=query]");
const $hostInput = $form.querySelector("input[name=host]");
const $methodSelect = $form.querySelector("select[name=method]");
const $formatSelect = $form.querySelector("select[name=format]");
const $fieldSelect = $form.querySelector("select[name=field]");
const $aLink = $form.querySelector("a#link");

$formatSelect.addEventListener("change", () => {
  const format = $formatSelect.value;
  $fieldSelect.disabled = format === "html";
  if ($fieldSelect.disabled) $fieldSelect.value = "";
});

$form.addEventListener("submit", (e) => e.preventDefault());

function generateForm(format, fields) {
  const $form = `<form action="//<?= $domain ?>" method="POST">
  <input type="text" name="ip" value="8.8.8.8" readonly>
  <input type="submit">
</form>`;

}

function generatePath(parts) {
  return parts.filter((p) => !!p).join("/");
}

function generateQuery(format, params) {
  return `${format || ""}?${Object.entries(params)
    .filter(([, v]) => !!v)
    .map(([k, v]) => `${k}=${v}`)
    .join("&")}`;
}

function generateLink() {
  const { protocol, host } = window.location;
  const params = {
    host: $hostInput.value,
    field: $fieldSelect.value,
  };
  const path =
    $methodSelect.value === "get"
      ? generateQuery($formatSelect.value, params)
      : generatePath([$formatSelect.value, ...Object.values(params)]);
  const link = `${protocol}//${host}/${path}`;
  $aLink.innerHTML = link;
  $aLink.href = link;
}

function generate() {
  $methodSelect.value === "post" ? generateForm() : generateLink();
}

[$hostInput, $methodSelect, $formatSelect, $fieldSelect].forEach((f) =>
  f.addEventListener("change", generate)
);
generate();
