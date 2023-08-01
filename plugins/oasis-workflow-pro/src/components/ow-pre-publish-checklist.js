/**
 * WordPress Dependencies
 */
const { __ } = wp.i18n;
const { Component } = wp.element;
const { withSelect } = wp.data;
const { compose } = wp.compose;
const { PanelRow } = wp.components;

export class PrePublishChecklist extends Component {
    constructor() {
        super(...arguments);

        this.state = {};
    }

    render() {
        return (
            <div>
                <h2>{__("Pre Publish Checklist ", "oasisworkflow") + ":"}</h2>
                {this.props.checklist.map((item) => (
                    <PanelRow key={item.value}>
                        <label>
                            <input type="checkbox" name="" onChange={this.props.onChange} value={item.value} />
                            {item.label}
                        </label>
                    </PanelRow>
                ))}
            </div>
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
])(PrePublishChecklist);
