/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

var translatedMessage;

var ddt = (function () {
    return {
        /**
         * @param {string} message The message to translate.
         * @param {object} $params Parameter to change within text as json string, i.e.: {fist_name: 'Veronica', last_name:'Hunter'}
         * @returns {string}
         */
        t: function (message, $params) {
            if (typeof (languageItems) !== 'undefined' && typeof (languageItems.getLanguageItems) === 'function') {
                var $messages = languageItems.getLanguageItems();
                if (typeof ($messages) !== 'undefined') {
                    var hash = hex_md5(message);
                    if (typeof ($messages[hash]) !== 'undefined') {
                        message = $messages[hash];
                    }
                }
            }
            if (typeof ($params) !== 'undefined') {
                for (search in $params) {
                    message = message.replace('{' + search + '}', $params[search]);
                }
            }
            return message;
        }
    };
})();