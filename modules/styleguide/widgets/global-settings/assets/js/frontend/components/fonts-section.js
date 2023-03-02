import React from 'react';
import styled from "styled-components";
import SectionTitle from "./section-title";
import Font from "./font";

const Wrapper = styled.div`
  display: flex;
  flex-direction: column;
`;

const FontsSection = ( { title, source } ) => {

	return (
		<>
			<SectionTitle> { title } </SectionTitle>
			<Wrapper>
				{ source.map( ( font ) => <Font key={ font._id }
				                                font={ font }
				/> ) }
			</Wrapper>
        </>
	);
};

export default FontsSection;