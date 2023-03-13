import styled from 'styled-components';

const LoaderWrapper = styled.div`
`;

export default function Loader() {
	return (
		<LoaderWrapper>
			<div id="elementor-styleguide-state-loading">
				<i className="eicon-loading eicon-animation-spin"></i>
			</div>
		</LoaderWrapper>
	);
}
