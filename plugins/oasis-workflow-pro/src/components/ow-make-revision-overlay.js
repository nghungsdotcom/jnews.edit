/**
 * WordPress Dependencies
 */
const { __ } = wp.i18n;
const { Component } = wp.element;
const { Button, Modal, PanelRow, Spinner } = wp.components;

export class MakeRevisionOverlay extends Component {
    render() {
        return (
            <Modal title={this.props.buttonText} onRequestClose={this.props.pageBack}>
                <PanelRow>
                    <p>{__(this.props.revisionOverlayText)}</p>
                </PanelRow>
                <PanelRow>
                    <Button isLink onClick={this.props.pageBack}>
                        {__("Cancel", "oasisworkflow")}
                    </Button>
                    <Button
                        type="submit"
                        isPrimary
                        focus="true"
                        disabled={this.props.revisionButtonDisable}
                        onClick={this.props.checkExistingRevision}
                    >
                        {this.props.buttonText}
                    </Button>
                    {this.props.submitSpinner == "show" ? <Spinner /> : ""}
                </PanelRow>
            </Modal>
        );
    }
}

export default MakeRevisionOverlay;
