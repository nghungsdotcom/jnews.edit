/**
 * WordPress Dependencies
 */
const { __ } = wp.i18n;
const { pick } = lodash;
const { Component, createRef } = wp.element;
const { compose } = wp.compose;
const { PanelBody, PanelRow, SelectControl, TextareaControl, Button, Modal, Spinner } = wp.components;
const { withSelect, withDispatch } = wp.data;


import { getActionHistoryIdFromURL, getTaskUserFromURL } from "../util";
// react select
import Select from 'react-select';
import DOMPurify from "dompurify";

export class Reassign extends Component {
    constructor() {
        super(...arguments);

        this.reassign = createRef();

        this.state = {
            assignActorLabel: __("Available Actor(s)", "oasisworkflow"),
            buttonText: __("Reassign", "oasisworkflow"),
            mandatoryComments: "",
            hideButton: true,
            hideForm: false,
            isOpen: false,
            isBusy: false,
            availableAssignees: [],
            selectedAssignees: [],
            actionHistoryId: getActionHistoryIdFromURL(),
            taskUser: getTaskUserFromURL(),
            comments: "",
            noAssigneeMessage: "hide",
            submitSpinner: "hide",
            submitButtonDisable: false,
            validationErrors: [],
            successMessage: ""
        };

        this.handleReassign = this.handleReassign.bind(this);
    }

    componentDidMount() {
        let customWorkflowTerminology = this.props.owSettings.terminology_settings.oasiswf_custom_workflow_terminology;
        let workflowSettings = this.props.owSettings.workflow_settings;

        if (customWorkflowTerminology) {
            let assignActorLabel = customWorkflowTerminology.assignActorsText;
            this.setState({
                assignActorLabel
            });
        }

        if (workflowSettings) {
            let mandatoryComments = workflowSettings.oasiswf_comments_setting;
            this.setState({
                mandatoryComments
            });
        }

        const { userCap } = this.props;
        if (userCap.user_can.ow_reassign_task) {
            this.setState({
                hideButton: false,
                isBusy: false
            });
        }

        // fetch assignees
        wp.apiFetch({
            path:
                "/oasis-workflow/v1/workflows/reassign/assignees/actionHistoryId=" +
                this.state.actionHistoryId +
                "/taskUser=" +
                this.state.taskUser,
            method: "GET"
        }).then(
            (results) => {
                if (results.assignee_count !== 0) {
                    let availableAssignees = [];
                    let assignees = results.user_info;
                    let assigneeData = assignees.map((users) => pick(users, ["ID", "name"]));
                    assigneeData.map((users) => {
                        availableAssignees.push({ label: users.name, value: users.ID });
                    });
                    this.setState({
                        availableAssignees
                    });
                } else {
                    this.setState({
                        noAssigneeMessage: "show",
                        hideForm: true
                    });
                }
            },
            (err) => {
                console.log(err);
                return err;
            }
        );
    }

    /**
     * Show the Reassign modal dialog
     */
    showReassignModal() {
        this.setState({
            isOpen: true
        });
    }

    /**
     * handle form submit for reassign
     */
     async handleReassign(event) {

        event.preventDefault();

        this.setState({
            isBusy: true,
            submitSpinner: "show",
            submitButtonDisable: true
        });

        let form_data = {
            post_id: this.props.postId,
            task_user: this.state.taskUser,
            history_id: this.state.actionHistoryId,
            assignees: this.state.selectedAssignees.map(assignee => assignee.value),
            comments: this.state.comments
        };

        await this.props.onSave();

        const errors = this.validateReassign(form_data);

        if (errors.length > 0) {
            event.preventDefault();
            this.setState({
                validationErrors: errors,
                submitSpinner: "hide",
                submitButtonDisable: false,
                isBusy: false
            });
            // scroll to the top, so that the user can see the error
            this.reassign.current.scrollIntoView();
            return;
        }

        this.setState({
            validationErrors: []
        });

        wp.apiFetch({ path: "/oasis-workflow/v1/workflows/reassign/", method: "POST", data: form_data }).then(
            (submitResponse) => {
                if (submitResponse.isError == true) {
                    this.setState({
                        validationErrors: submitResponse.errorResponse,
                        submitSpinner: "hide",
                        submitButtonDisable: false
                    });
                    // scroll to the top, so that the user can see the error
                    this.reassign.current.scrollIntoView();
                } else {
                    console.log('holla');
                    this.setState({
                        successMessage: submitResponse.successResponse,
                        hideForm: true
                    });

                    // Redirect user to inbox page
                    if (submitResponse.redirect_link !== "") {
                        window.location.href = DOMPurify.sanitize(submitResponse.redirect_link);
                    }
                }
            },
            (err) => {
                console.log(err);
                return err;
            }
        );
    }

    validateReassign(data) {
        const errors = [];

        if (data.assignees.length === 0) {
            errors.push(__("No assigned actor(s).", "oasisworkflow"));
        }

        if (data.comments === "" && this.state.mandatoryComments === "mandatory") {
            errors.push(__("Please enter comments.", "oasisworkflow"));
        }

        return errors;
    }

    render() {
        const {
            hideButton,
            buttonText,
            isOpen,
            assignActorLabel,
            noAssigneeMessage,
            submitSpinner,
            submitButtonDisable,
            validationErrors,
            hideForm,
            successMessage
        } = this.state;
        const { isPostInWorkflow, isCurrentPostPublished } = this.props;

        // if post is NOT in workflow, then do not show abort button
        if (hideButton || !isPostInWorkflow || isCurrentPostPublished) {
            return "";
        }

        return (
            <PanelBody>
                <Button focus="true" onClick={this.showReassignModal.bind(this)} isLink>
                    {buttonText}
                </Button>
                {isOpen && (
                    <Modal
                        ref={this.reassign}
                        title={buttonText}
                        onRequestClose={() => this.setState({ isOpen: false })}
                    >
                        {validationErrors.length !== 0 ? (
                            <div id="owf-error-message" className="notice notice-error is-dismissible">
                                {validationErrors.map((error) => (
                                    <p key={error}>{error}</p>
                                ))}
                            </div>
                        ) : (
                            ""
                        )}
                        {noAssigneeMessage == "show" ? (
                            <div id="owf-error-message" className="notice notice-error is-dismissible">
                                {__("No users found to reassign", "oasisworkflow")}
                            </div>
                        ) : (
                            ""
                        )}
                        {successMessage !== "" ? (
                            <div id="owf-success-message" className="notice notice-success is-dismissible">
                                {<p key={successMessage}>{successMessage}</p>}
                            </div>
                        ) : (
                            ""
                        )}
                        {!hideForm && (
                            <form className="reusable-block-edit-panel owf-reassign" onSubmit={this.handleReassign}>
                                <div className="ow-select2-wrap">
                                    <label htmlFor={'assignActor'}>{assignActorLabel + ":"}</label>
                                    <Select
                                        inputId="assignActor"
                                        className="ow-select2"
                                        value={this.props.value}
                                        options={this.state.availableAssignees}
                                        onChange={(selectedAssignees) => this.setState({ selectedAssignees })}
                                        isMulti='true'
                                    />
                                    <span>{__("select actor(s) to reassign the task", "oasisworkflow")}</span>
                                </div>
                                <TextareaControl
                                    label={__("Comments", "oasisworkflow") + ":"}
                                    value={this.state.comments}
                                    onChange={(comments) => this.setState({ comments })}
                                />
                                <PanelRow>
                                    <Button isLink onClick={() => this.setState({ isOpen: false })}>
                                        {__("Cancel", "oasisworkflow")}
                                    </Button>
                                    <Button
                                        type="submit"
                                        isBusy={this.state.isBusy}
                                        isPrimary
                                        disabled={submitButtonDisable}
                                        focus="true"
                                    >
                                        {buttonText}
                                    </Button>
                                    {submitSpinner == "show" ? <Spinner /> : ""}
                                </PanelRow>
                            </form>
                        )}
                    </Modal>
                )}
            </PanelBody>
        );
    }
}

export default compose([
    withSelect((select) => {
        const { getCurrentPostId, isCurrentPostPublished } = select("core/editor");
        const { getUserCapabilities, getPostInWorkflow, getOWSettings } = select("plugin/oasis-workflow");
        return {
            postId: getCurrentPostId(),
            userCap: getUserCapabilities(),
            isPostInWorkflow: getPostInWorkflow(),
            owSettings: getOWSettings(),
            isCurrentPostPublished: isCurrentPostPublished()
        };
    }),
    withDispatch((dispatch) => ({
        onSave: dispatch("core/editor").savePost
    }))
])(Reassign);
