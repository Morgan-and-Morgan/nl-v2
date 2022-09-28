import { createForm } from '@morgan-morgan/core';

// require("./components/form-intake-registration");

// Welcome to Abo-V2 console message.
console.log('Welcome to NationLaw V2!');

console.log(` * * ${window.MM.forms.length} forms * * `);
window.MM.forms.forEach((f) => {
    createForm(f.target, { formId: f.formId });
});
