import React from 'react';
import { withStyles } from '@material-ui/core/styles';
import { ReferenceArrayInput, AutocompleteArrayInput, required, minLength } from 'react-admin';

const styles = {
    chip: {
        margin: '3px',
    },
    container: {
        '& div': {
            flexWrap: 'wrap',
            marginTop: '18px',
            maxWidth: '800px'
        },
        marginBottom: '8px'
    },
    suggestionsContainerOpen: {
        marginLeft: '100px'
    }
};

const validateQuestion = [required(), minLength(1, 'There must be 1 question at least')];

export const MyReferenceArrayInput = withStyles(styles)(({ classes }) =>
    <ReferenceArrayInput source="questions" resource="questions" reference="questions" label="Questions" validate={validateQuestion}>
        <AutocompleteArrayInput optionText={question => question.content.substring(0, 130)} classes={classes} allowEmpty/>
    </ReferenceArrayInput>
);
