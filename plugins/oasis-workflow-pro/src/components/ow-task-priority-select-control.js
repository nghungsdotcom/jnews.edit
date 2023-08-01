/**
 * WordPress Dependencies
 */
const { __ } = wp.i18n;
const { Component } = wp.element;
const { SelectControl, Spinner } = wp.components;
const { pick } = lodash;
const { withSelect } = wp.data;
const { compose } = wp.compose;

import HelpImage from "../images/help.png";

export class TaskPriorities extends Component {
    constructor() {
        super(...arguments);

        this.state = {
            priorityLabel: __("Priority", "oasisworkflow"),
            priorities: [],
            displayPriority: "",
            prioritySpinner: "hide"
        };
    }

    componentDidMount() {
        let customWorkflowTerminology = this.props.owSettings.terminology_settings.oasiswf_custom_workflow_terminology;
        let workflowSettings = this.props.owSettings.workflow_settings;

        if (customWorkflowTerminology) {
            let priorityLabel = customWorkflowTerminology.taskPriorityText;
            this.setState({
                priorityLabel
            });
        }

        if (workflowSettings) {
            let displayPriority = workflowSettings.oasiswf_priority_setting;
            this.setState({
                displayPriority,
                prioritySpinner: "show"
            });
        }

        // fetch priority List
        wp.apiFetch({ path: "/oasis-workflow/v1/priorities", method: "GET" }).then(
            (data) => {
                let priorityData = data.map((priority) => pick(priority, ["key", "value"]));
                let priorities = [];
                priorityData.map((priority) => {
                    priorities.push({ label: priority.value, value: priority.key });
                });
                this.setState({
                    priorities: priorities,
                    prioritySpinner: "hide"
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
        const { priorityLabel, displayPriority } = this.state;
        return (
            <div>
                <div className="owf-spinner">{this.state.prioritySpinner == "show" ? <Spinner /> : ""}</div>
                {displayPriority === "enable_priority" ? (
                    <div>
                        <label>
                            {priorityLabel + ": "}
                            <a href="#" title={__("Post/Article Priority", "oasisworkflow")} className="tooltip">
                                <span title="">
                                    <img src={HelpImage} className="help-icon" />
                                </span>
                            </a>
                        </label>
                        <SelectControl
                            value={this.props.value}
                            options={this.state.priorities}
                            onChange={this.props.onChange}
                        />
                    </div>
                ) : (
                    ""
                )}
            </div>
        );
    }
}

export default compose([
    withSelect((select) => {
        const { getOWSettings } = select("plugin/oasis-workflow");
        return {
            owSettings: getOWSettings()
        };
    })
])(TaskPriorities);
