/**
 * WordPress Dependencies
 */
const { __ } = wp.i18n;
const { Component } = wp.element;
const { SelectControl, Spinner } = wp.components;
const { withSelect } = wp.data;
const { compose } = wp.compose;
const { pick } = lodash;

export class WorkflowSelectControl extends Component {
    constructor() {
        super(...arguments);

        this.state = {
            workflows: [],
            workflowSpinner: "hide"
        };
    }

    componentDidMount() {
        // fetch workflow list
        this.setState({
            workflowSpinner: "show"
        });
        let postId = this.props.postId;
        wp.apiFetch({ path: "/oasis-workflow/v1/workflows" + "/postId=" + postId, method: "GET" }).then(
            (data) => {
                let workflowData = data.map((workflow) => pick(workflow, ["ID", "name", "version"]));
                let workflows = [];
                if (workflowData.length > 1) {
                    // we have more than one valid workflow, so add a empty option value
                    workflows.push({ label: "", value: "" });
                }
                workflowData.map((workflow) => {
                    if (workflow.version == 1) {
                        workflows.push({ label: workflow.name, value: workflow.ID });
                    } else {
                        workflows.push({ label: workflow.name + " (" + workflow.version + ")", value: workflow.ID });
                    }

                    if (workflowData.length == 1) {
                        // only one valid workflow, then autoselect and call onChange
                        this.props.onChange(workflow.ID);
                    }
                });
                this.setState({
                    workflows,
                    workflowSpinner: "hide"
                });
                return data;
            },
            (err) => {
                console.log(err);
                return err;
            }
        );
    }

    render() {
        return (
            <div>
                <div className="owf-spinner">{this.state.workflowSpinner == "show" ? <Spinner /> : ""}</div>
                <SelectControl
                    label={__("Select Workflow", "oasisworkflow") + ":"}
                    value={this.props.value}
                    options={this.state.workflows}
                    onChange={this.props.onChange}
                />
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
])(WorkflowSelectControl);
