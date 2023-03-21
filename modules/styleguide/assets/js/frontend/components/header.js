import React, { useContext } from 'react';
import styled from 'styled-components';
import { ActiveContext } from '../contexts/active-context';
import DivBase from './global/div-base';
import InnerWrapper from './global/inner-wrapper';

const Button = styled.button`
	font-size: 16px;
	height: 100%;
	font-weight: 500;
	font-style: normal;
	text-decoration: none;
	line-height: 1.5em;
	letter-spacing: 0;
	color: var( --e-a-color-txt );
	border: none;
	background: none;
	text-transform: capitalize;
	font-family: Roboto, sans-serif;
	padding: 0;

	&:hover {
		background: none;
		color: var( --e-a-color-txt-hover );
	}

	&:focus {
		outline: none;
		background: none;
		color: #51585e;
	}
`;

const AreaButton = ( props ) => {
	const { activateArea } = useContext( ActiveContext );

	const { area, children } = props;
	const onClick = () => {
		activateArea( area );
	};

	// TODO: Add hover/active states

	return (
		<Button variant="transparent"
			size="s"
			onClick={ onClick }
		>
			{ children }
		</Button>
	);
};

const Wrapper = styled( DivBase )`
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 50px;
	background: #ffffff;
	border-bottom: 1px solid #C2CBD2;
	z-index: 1;
`;

const ButtonsWrapper = styled( DivBase )`
	display: flex;
	justify-content: flex-end;
	flex-grow: 1;
	gap: 8px;
`;

const Title = styled.h2`
	color: #0C0D0E;
	font-family: Roboto, sans-serif;
	font-size: 16px;
	font-weight: 600;
	text-transform: capitalize;
	font-style: normal;
	text-decoration: none;
	line-height: 1.2em;
	letter-spacing: 0;
	word-spacing: 0;
	margin: 0;
`;

export default function Header() {
	return (
		<Wrapper>
			<InnerWrapper>
				<Title>{ __( 'Style Guide Preview', 'elementor' ) }</Title>
				<ButtonsWrapper>
					<AreaButton area={ 'colors' }>{ __( 'Colors', 'elementor' ) }</AreaButton>
					<AreaButton area={ 'fonts' }>{ __( 'Fonts', 'elementor' ) }</AreaButton>
				</ButtonsWrapper>
			</InnerWrapper>
		</Wrapper>
	);
}

AreaButton.propTypes = {
	area: PropTypes.string.isRequired,
	children: PropTypes.node.isRequired,
};
