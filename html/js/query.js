const $form = document.querySelector("form[name=query]");
const $hostInput = $form.querySelector("input[name=host]");
const $formatSelect = $form.querySelector("select[name=format]");
const $fieldSelect = $form.querySelector("select[name=field]");
const $aLink = $form.querySelector("a#link");

$formatSelect.addEventListener("change", e => {
  const format = $formatSelect.value;
  $fieldSelect.disabled = format !== "";
  if ($fieldSelect.disabled) $fieldSelect.value = "";
});

$form.addEventListener("submit", e => {
  e.preventDefault();
});

function generateLink() {
  const { protocol, host } = window.location;
  const fields = [
    $hostInput.value,
    $formatSelect.value,
    $fieldSelect.value
  ].filter(f => f !== "");
  const link = `${protocol}//${host}/${fields.join("/")}`;
  $aLink.innerHTML = link;
  $aLink.href = link;
}

[$formatSelect, $hostInput, $fieldSelect].forEach(f =>
  f.addEventListener("change", generateLink)
);
generateLink();
