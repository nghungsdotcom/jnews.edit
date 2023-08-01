/**
 * WordPress Dependencies
 */
const { DateTimePicker } = wp.components;
const { withSelect, withDispatch } = wp.data;
const { compose } = wp.compose;

export function OWDueDatePicker({ dueDate, onUpdateDate }) {
    return <DateTimePicker key="ow-date-picker" onChange={onUpdateDate} currentDate={dueDate} />;
}

export default compose([
    withSelect((select) => {
        const { getDueDate } = select("plugin/oasis-workflow");
        return {
            dueDate: getDueDate()
        };
    }),
    withDispatch((dispatch) => {
        return {
            onUpdateDate(dueDate) {
                dispatch("plugin/oasis-workflow").setDueDate({ dueDate });
            }
        };
    })
])(OWDueDatePicker);
