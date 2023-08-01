/**
 * WordPress Dependencies
 */
const { withSelect } = wp.data;
const { dateI18n } = wp.date;

export function OWDueDateLabel({ date }) {
    return dateI18n("M d, Y", date);
}

export default withSelect((select) => {
    return {
        date: select("plugin/oasis-workflow").getDueDate()
    };
})(OWDueDateLabel);
