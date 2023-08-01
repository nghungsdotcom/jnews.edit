const { __ } = wp.i18n;
const { unregisterPlugin } = wp.plugins;
const { pick } = lodash;
const el = wp.element.createElement;

import DOMPurify from "dompurify";

/**
 * gets action history Id from URL
 *
 */
 export function getActionHistoryIdFromURL() {
    let params = new RegExp("[?&]oasiswf=([^&#]*)").exec(DOMPurify.sanitize(window.location.href));
    if (params == null) {
        let oasiswf_id = document.getElementById('hi_oasiswf_id');
        if (typeof(oasiswf_id) == 'undefined' || oasiswf_id == null) {
            return null;
        }
        return oasiswf_id.value;
    } else {
        return decodeURI(params[1]) || 0;
    }
}

export function getPostTypeFromURL() {
    let params = new RegExp("[?&]post_type=([^&#]*)").exec(DOMPurify.sanitize(window.location.href));

    console.log(params);
    if (params == null) {
        return "post";
    } else {
        return decodeURI(params[1]) || 0;
    }
}

/**
 * get task user from URL
 */
export function getTaskUserFromURL() {
    let params = new RegExp("[?&]user=([^&#]*)").exec(DOMPurify.sanitize(window.location.href));
    if (params == null) {
        return null;
    } else {
        return decodeURI(params[1]) || 0;
    }
}

/**
 * Prepare sign off actions as per the process type
 * @param {String} process
 */
export function getSignOffActions(success_action, failure_action) {
    let actions = [];

    actions.push({ label: "", value: "" }, { label: success_action, value: "complete" });

    if (failure_action) {
        actions.push({ label: failure_action, value: "unable" });
    }
    return actions;
}

/**
 * Get step assignees
 * @param {*} data
 */
export function getStepAssignees(data) {
    let assignees = data.assignees;
    let availableAssignees = [];
    let selectedAssignees = [];
    let isAssignToAll = data.assignToAll;

    // Sort Post Author at top of the assignee list
    let postAuthor = "";
    let substring = "Post Author";
    for (var i = 0; i < assignees.length; i++) {
        if (assignees[i].name.indexOf(substring) !== -1) {
            postAuthor = assignees[i];
        }
    }

    // show post author on the top
    assignees.sort(function (x, y) {
        return x === postAuthor ? -1 : y === postAuthor ? 1 : 0;
    });

    let assigneeData = assignees.map((users) => pick(users, ["ID", "name"]));
    assigneeData.map((users) => {
        availableAssignees.push({ label: users.name, value: users.ID });
    });

    // Only one assignee than select it by default
    if (availableAssignees.length == 1) {
        selectedAssignees = [availableAssignees[0]];
    }

    if (isAssignToAll) {
        let allAssignees = [];
        assigneeData.map((users) => {
            allAssignees.push(users.ID);
        });
        selectedAssignees = allAssignees;
    }

    return { availableAssignees: availableAssignees, selectedAssignees: selectedAssignees };
}

export const pluginIcon = el(
    "svg",
    {
        width: 20,
        height: 20
    },
    el("path", {
        d:
            "M20,11.647c0-5.204-4.478-9.424-10-9.424c-5.523,0-10,4.22-10,9.424c0,0.594,0.061,1.174,0.172,1.734c0.077-5.232,4.371-9.449,9.659-9.449c5.337,0,9.664,4.295,9.664,9.593c0,0.182-0.008,0.362-0.019,0.541l0.199-0.037C19.886,13.268,20,12.47,20,11.647z"
    }),
    el("polygon", {
        points: "7.471,9.916 9.197,12.597 6.949,18 3.876,18 	"
    }),
    el("polygon", {
        points: "15.553,10.996 12.732,18 15.911,18 17.868,12.219 	"
    }),
    el("polygon", {
        points: "14.355,11.07 17.818,8.699 18.838,12.771 	"
    }),
    el("polygon", {
        points: "7.484,9.762 10.317,9.767 15.911,17.674 12.74,17.674 	"
    }),
    el("polygon", {
        points: "0.379,12.984 1.659,9.94 6.964,17.674 3.876,17.674 	"
    })
);

// export function checkIsRoleApplicable(postType) {
//    wp.apiFetch({ path: '/oasis-workflow/v1/workflows/submit/checkRoleCapability/postType=' + postType, method: 'GET' }).then(
//       (response) => {
//          if(!response.is_role_applicable) {
//             unregisterPlugin( 'oasis-workflow-pro-plugin' );
//          }
//       },
//       (err) => {
//          return err;
//       }
//    );
// }
