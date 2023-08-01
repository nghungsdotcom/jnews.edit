/**
 * WordPress Dependencies
 */
const { registerStore } = wp.data;
// const { apiFetch } = wp.apiFetch;

/**
 * Internal dependencies
 */
import reducer from "./reducer";
import * as selectors from "./selectors";
import * as actions from "./actions";

/**
 * Module Constants
 */
const MODULE_KEY = "plugin/oasis-workflow";

const store = registerStore(MODULE_KEY, {
    reducer,
    selectors,
    actions,
    controls: {
        FETCH_OW_SETTINGS(action) {
            return wp.apiFetch({ path: action.path });
        },
        FETCH_USER_CAPABILITIES(action) {
            return wp.apiFetch({ path: action.path });
        },
        FETCH_EDITORIAL_COMMENTS_ACTIVATION(action) {
            return wp.apiFetch({ path: action.path });
        }
    },
    resolvers: {
        *getOWSettings() {
            const path = "/oasis-workflow/v1/settings";
            const owSettings = yield actions.fetchOWSettings(path);
            return actions.setOWSettings(owSettings);
        },
        *getUserCapabilities() {
            const path = "/oasis-workflow/v1/usercap";
            const userCap = yield actions.fetchUserCapabilities(path);
            return actions.setUserCapabilities(userCap);
        },
        *getEditorialCommentsActivation() {
            const path = "/oasis-workflow/v1/editorialComments";
            const activation = yield actions.fetchEditorialCommentsActivation(path);
            return actions.setEditorialCommentsActivation(activation);
        }
    }
});

export default store;
