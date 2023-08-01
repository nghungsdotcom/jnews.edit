/**
 * WordPress Dependencies
 */
const { __ } = wp.i18n;
const { Fragment, Component, createRef } = wp.element;
const { compose } = wp.compose;
const { PanelBody, PanelRow, Dropdown, CheckboxControl, TextareaControl, Button, Spinner } = wp.components;
const { withSelect, withDispatch } = wp.data;
const { PostScheduleLabel, PostSchedule } = wp.editor;

import DOMPurify from "dompurify";
/**
 * Internal dependencies
 */

import PrePublishChecklist from "./ow-pre-publish-checklist";
import { getActionHistoryIdFromURL, getTaskUserFromURL } from "../util";

export class SignoffLastStep extends Component {
    constructor() {
        super(...arguments);

        this.state = {
            signoffButtonText: __("Sign Off", "oasisworkflow"),
            continueToSignoffText: __("Continue to Signoff", "oasisworkflow"),
            comments: "",
            actionHistoryId: getActionHistoryIdFromURL(),
            taskUser: getTaskUserFromURL(),
            isImmediatelyChecked: false,
            originalPublishDate: this.props.publishDate,
            selectedChecklist: [],
            validationErrors: [],
            errorType: "",
            byPassWarning: false,
            loader: "hide",
            submitSpinner: "hide",
            submitButtonDisable: false
        };
    }

    componentDidMount() {
        let customWorkflowTerminology = this.props.owSettings.terminology_settings.oasiswf_custom_workflow_terminology;

        if (customWorkflowTerminology) {
            let signoffButtonText = customWorkflowTerminology.signOffText;
            let continueToSignoffText = customWorkflowTerminology.continueToSignoffText;
            this.setState({
                signoffButtonText,
                continueToSignoffText
            });
        }
    }

    /**
     * Handle selected pre publish checklist
     * @param {*} checklist
     */
    selectPrePublishChecklist(checklist) {
        // Get current state
        const options = this.state.selectedChecklist;
        let index;

        // check if the check box is checked or unchecked
        if (checklist.target.checked) {
            options.push(checklist.target.value);
        } else {
            index = options.indexOf(checklist.target.value);
            options.splice(index, 1);
        }
        this.setState({
            selectedChecklist: options
        });
    }

    /**
     * The user wants to continue to sign off, so we need to bypass the warnings
     * @param {*} event
     */
    handleContinueToSignoff(event) {
        // call handleWorkflowComplete as callback of setState, so that it's called after the state is set
        this.setState(
            {
                byPassWarning: true
            },
            () => {
                this.handleWorkflowComplete();
            }
        );
    }

    /**
      * validate sign off
      * @param {Object} data
      */
     validateSignoff(data) {
        const errors = [];

        if( typeof window.acf !== "undefined" ) {
            // Check if ACF field valid or not if they exists
            let ACFCheck = window.workflowSubmitWithACF(); // example
            if( ACFCheck === 'invalid' ) {
                errors.push(__("Please enter ACF required fields.", "oasisworkflow"));
            }
        }

        return errors;
    }

    /**
     * handle successful completion of workflow
     */
     async handleWorkflowComplete(event) {
        // event.preventDefault();

        this.setState({
            submitSpinner: "show",
            submitButtonDisable: true
        });

        let form_data = {
            post_id: this.props.postId,
            history_id: this.state.actionHistoryId,
            immediately: this.state.isImmediatelyChecked,
            task_user: this.state.taskUser,
            publish_datetime: this.props.publishDate,
            pre_publish_checklist: this.state.selectedChecklist,
            by_pass_warning: this.state.byPassWarning
        };

        // save the post
        await this.props.onSave();

        const errors = this.validateSignoff(form_data);
 
         if (errors.length > 0) {
             this.setState({
                 validationErrors: errors,
                 submitSpinner: "hide",
                 submitButtonDisable: false,
                 errorType: ""
             });
  
             return;
         }
 
         this.setState({
             validationErrors: []
         });

        // TODO: introducing a delay to allow the post to be saved and then invoke the sign off
        var that = this;
        that.invokeSignoffAPI(form_data);
    }

    // redirect invoke
    redirectTrigger(url) {
        this.setState({
            loader: "show"
        });
        window.location.href = DOMPurify.sanitize(url);
    }
    
    // page refresh invoke
    refreshTrigger(response) {
        this.props.handleResponse(response);
        this.props.pageRefresh();
    }

    invokeSignoffAPI(form_data) {
        wp.apiFetch({
            path: "/oasis-workflow/v1/workflows/signoff/workflowComplete/",
            method: "POST",
            data: form_data
        }).then(
            (submitResponse) => {
                if (submitResponse.success_response == false) {
                    this.setState({
                        validationErrors: submitResponse.validation_error,
                        errorType: submitResponse.error_type
                    });
                } else {

                    // hooking workflow complete for js trigger
                    var evt = new CustomEvent("ow_workflow_completed", {detail: submitResponse});
                    document.dispatchEvent(evt);
                    
                    // Redirect user to inbox page
                    if (submitResponse.redirect_link !== "") {
                        // update post with laststep status.
                        const postUpdate = async () => {
                            await this.props.editPost({ status: submitResponse.new_post_status });
                            await this.props.onSave();
                            this.redirectTrigger(submitResponse.redirect_link);
                        }
                        postUpdate();
                    } else {
                        // update post with laststep status.
                        const postUpdate = async () => {
                            await this.props.editPost({ status: submitResponse.new_post_status });
                            await this.props.onSave();
                            this.refreshTrigger(submitResponse);
                        }
                        postUpdate();
                    }
                }
                return submitResponse;
            },
            (err) => {
                console.log(err);
                return err;
            }
        );
    }

    /**
     * handle cancellation of workflow on the last step
     */
    handleWorkflowCancel(event) {
        let form_data = {
            post_id: this.props.postId,
            history_id: this.state.actionHistoryId,
            comments: this.state.comments,
            task_user: this.state.taskUser
        };

        wp.apiFetch({
            path: "/oasis-workflow/v1/workflows/signoff/workflowCancel/",
            method: "POST",
            data: form_data
        }).then(
            (submitResponse) => {
                this.props.handleResponse(submitResponse);
            },
            (err) => {
                console.log(err);
                return err;
            }
        );
    }

    /**
     * handle immediately checkbox change
     * @param {boolean} checked
     */
    onImmediatelyChange(checked) {
        let currentDate = new Date();
        let newDate = "";
        if (checked) {
            this.setState({
                isImmediatelyChecked: true
            });
            newDate = currentDate; //publish date set to now
        } else {
            this.setState({
                isImmediatelyChecked: false
            });
            newDate = this.state.originalPublishDate; // publish date set to the original date
        }

        this.props.editPost({ date: newDate });
        this.props.onSave();
    }

    render() {
        const { isSaving, postMeta } = this.props;
        const {
            isImmediatelyChecked,
            signoffButtonText,
            continueToSignoffText,
            validationErrors,
            errorType,
            submitSpinner,
            submitButtonDisable,
            loader
        } = this.state;

        if (loader === "show") {
            return (
                <div>
                    <PanelBody>{__("redirecting...", "oasisworkflow")}</PanelBody>
                </div>
            );
        }

        return (
            <div>
                {validationErrors.length !== 0 ? (
                    <div id="owf-error-message" className="notice notice-error is-dismissible">
                        {validationErrors.map((error) => (
                            <p key={error}>{error}</p>
                        ))}
                        {errorType == "warning" ? (
                            <p>
                                <Button isSecondary focus="true" onClick={this.handleContinueToSignoff.bind(this)}>
                                    {continueToSignoffText}
                                </Button>
                            </p>
                        ) : (
                            ""
                        )}
                    </div>
                ) : (
                    ""
                )}
                {this.props.stepDecision === "success" ? (
                    <div>
                        <PanelRow>
                            <div id="owf-success-message" className="notice notice-warning is-dismissible">
                                <p>
                                    {__(
                                        "This is the last step in the workflow. Are you sure to complete the workflow?",
                                        "oasisworkflow"
                                    )}
                                </p>
                                {postMeta && postMeta._oasis_original ? (
                                    <p>
                                        {" "}
                                        {__(
                                            "Signing off will copy over the contents of this revised article to the corresponding published/original article. This will happen either immediately or on the scheduled date/time.",
                                            "oasisworkflow"
                                        )}
                                    </p>
                                ) : (
                                    ""
                                )}
                            </div>
                        </PanelRow>
                        {this.props.checklist.length !== 0 ? (
                            <PrePublishChecklist
                                checklist={this.props.checklist}
                                onChange={this.selectPrePublishChecklist.bind(this)}
                            />
                        ) : (
                            ""
                        )}
                        <PanelRow className="edit-post-post-schedule">
                            <label>{__("Publish", "oasisworkflow") + ":"} </label>
                            <Dropdown
                                position="bottom left"
                                contentClassName="edit-post-post-schedule__dialog"
                                renderToggle={({ onToggle, isOpen }) => (
                                    <Fragment>
                                        <Button
                                            type="button"
                                            onClick={onToggle}
                                            aria-expanded={isOpen}
                                            aria-live="polite"
                                            isLink
                                        >
                                            <PostScheduleLabel />
                                        </Button>
                                    </Fragment>
                                )}
                                renderContent={() => <PostSchedule />}
                            />
                        </PanelRow>
                        <PanelRow>
                            <CheckboxControl
                                label={__("Publish Immediately?", "oasisworkflow")}
                                checked={isImmediatelyChecked}
                                onChange={this.onImmediatelyChange.bind(this)}
                            />
                        </PanelRow>
                        <PanelRow>
                            <Button
                                isPrimary
                                isBusy={isSaving}
                                focus="true"
                                onClick={this.handleWorkflowComplete.bind(this)}
                            >
                                {signoffButtonText}
                            </Button>
                        </PanelRow>
                    </div>
                ) : (
                    <div>
                        <PanelRow>
                            <div id="owf-success-message" className="notice notice-error is-dismissible">
                                <p>
                                    {__(
                                        "There are no further steps defined in the workflow. Do you want to cancel the post/page from the workflow?",
                                        "oasisworkflow"
                                    )}
                                </p>
                            </div>
                        </PanelRow>
                        <PanelRow>
                            <TextareaControl
                                label={__("Comments", "oasisworkflow") + ":"}
                                value={this.state.comments}
                                onChange={(comments) => this.setState({ comments })}
                            />
                        </PanelRow>
                        <PanelRow>
                            <Button
                                isPrimary
                                isBusy={isSaving}
                                focus="true"
                                onClick={this.handleWorkflowCancel.bind(this)}
                            >
                                {signoffButtonText}
                            </Button>
                        </PanelRow>
                    </div>
                )}
            </div>
        );
    }
}

export default compose([
    withSelect((select) => {
        const { getCurrentPostId, getEditedPostAttribute } = select("core/editor");
        const { getOWSettings } = select("plugin/oasis-workflow");
        return {
            postId: getCurrentPostId(),
            publishDate: getEditedPostAttribute("date"),
            postMeta: getEditedPostAttribute("meta"),
            owSettings: getOWSettings()
        };
    }),
    withDispatch((dispatch) => ({
        onSave: dispatch("core/editor").savePost,
        editPost: dispatch("core/editor").editPost,
        autosave: dispatch("core/editor").autosave,
        pageRefresh: dispatch("core/editor").refreshPost
    }))
])(SignoffLastStep);
