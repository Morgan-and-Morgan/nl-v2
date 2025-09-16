(function (Drupal, once) {
  // Universal promise waiting for an element to appear.
  function waitForElement(selector, context = document, timeout = 15000) {
    return new Promise((resolve, reject) => {
      const found = context.querySelector(selector);
      if (found) return resolve(found);

      const observer = new MutationObserver(() => {
        const el = context.querySelector(selector);
        if (el) {
          observer.disconnect();
          resolve(el);
        }
      });

      // Keep track of node additions in the current context.
      observer.observe(context, { childList: true, subtree: true });

      if (timeout) {
        setTimeout(() => {
          observer.disconnect();
          reject(new Error(`Timeout: не знайшов "${selector}" за ${timeout} мс`));
        }, timeout);
      }
    });
  }

  Drupal.behaviors.superfishToggle = {
    attach(context) {
      // Wait until the desired element appears in this context.
      waitForElement('a#superfish-main-toggle', context)
        .then(() => {
          // When it appears — process it only ONCE via once().
          once('nl-updates-superfish-toggle', 'a#superfish-main-toggle', context)
            .forEach((el) => {
              el.setAttribute('aria-label', 'Toggle mobile navigation');
            });
        })
        .catch((e) => console.debug(e.message));
    }
  };
})(Drupal, once);

