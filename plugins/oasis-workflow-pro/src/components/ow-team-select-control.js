/**
 * WordPress Dependencies
 */
const { __ } = wp.i18n;
const { Component } = wp.element;
const { SelectControl } = wp.components;
const { withSelect } = wp.data;
const { compose } = wp.compose;

export class TeamSelectControl extends Component {
    constructor() {
        super(...arguments);
    }

    render() {
        return (
            <SelectControl
                label={__("Assign To Team", "oasisworkflow")}
                value={this.props.value}
                options={this.props.options}
                onChange={this.props.onChange}
            />
        );
    }
}

export default compose([
    withSelect((select) => {
        const { getCurrentPostId } = select("core/editor");
        return {
            postId: getCurrentPostId()
        };
    })
])(TeamSelectControl);
