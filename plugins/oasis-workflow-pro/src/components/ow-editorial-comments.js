/**
 * WordPress Dependencies
 */
const { __ } = wp.i18n;
const { Component } = wp.element;
const { PanelBody, PanelRow } = wp.components;
const { withSelect, withDispatch } = wp.data;
const { compose } = wp.compose;

import DOMPurify from "dompurify";

export class EditorialComments extends Component {
    constructor() {
        super(...arguments);

        this.state = {
            displayComments: false,
            comments: ""
        };
    }

    componentDidMount() {
        let postId = this.props.postId;
        // Get Editorial Comments
        wp.apiFetch({ path: "/oasis-workflow/v1/comments/postId=" + postId, method: "GET" }).then(
            (data) => {
                this.setState({
                    displayComments: true,
                    comments: data.editorialComments
                });
            },
            (err) => {
                console.log(err);
                return err;
            }
        );
    }

    render() {
        const { displayComments, comments } = this.state;

        if (displayComments) {
            return (
                <div>
                    <PanelBody title={__("Editorial Comments", "oasisworkflow")} initialOpen={true}>
                        <PanelRow className="editorialComments">
                            <div dangerouslySetInnerHTML={{ __html: comments }}></div>
                        </PanelRow>
                    </PanelBody>
                </div>
            );
        } else {
            return null;
        }
    }
}

export default compose([
    withSelect((select) => {
        const { getCurrentPostId } = select("core/editor");
        const { getOWSettings } = select("plugin/oasis-workflow");
        return {
            postId: getCurrentPostId(),
            owSettings: getOWSettings()
        };
    }),
    withDispatch((dispatch) => ({
        onSave: dispatch("core/editor").savePost,
        autosave: dispatch("core/editor").autosave
    }))
])(EditorialComments);
