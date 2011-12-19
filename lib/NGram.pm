package NGram;
use Moose;
use List::Util qw[min max];

has 'reference_length' => (
	isa => 'Int',
	is => 'ro',
	writer => '_reference_length',
	default => 0
);

has 'machine_length' => (
	isa => 'Int',
	is => 'ro',
	writer => '_machine_length',
	default => 0
);

has 'reference_ngrams' => (
	isa => 'ArrayRef',
	is => 'ro',
	writer => '_reference_ngrams'
);

has 'common_ngrams' => (
	isa => 'ArrayRef', 
	is => 'ro', 
	writer => '_common_ngrams'
);

has 'missing_ngrams' => (
	isa => 'ArrayRef',
	is => 'ro',
	writer => '_missing_ngrams'
);

has 'redundant_ngrams' => (
	isa => 'ArrayRef',
	is => 'ro',
	writer => '_redundant_ngrams'
);


sub add_sentence {
	my $self = shift;
	my $reference = shift;
	my $machine = shift;
	
	my @reference_ngrams = $self->_count_ngrams( $reference );
	my @machine_ngrams = $self->_count_ngrams( $machine );

	$self->_add_reference_length( $reference );
	$self->_add_machine_length( $machine );
	
	$self->_add_reference_ngrams( \@reference_ngrams );
	$self->_add_common_ngrams( \@reference_ngrams, \@machine_ngrams );
	$self->_add_missing_ngrams( \@reference_ngrams, \@machine_ngrams );
	$self->_add_redundant_ngrams( \@reference_ngrams, \@machine_ngrams );
}


sub get_bleu {
	my $self = shift;
	my $brevity_penalty = $self->_count_brevity_penalty(); 
	my $geometric_average = $self->_count_geometric_average( "-inf" );

	return sprintf( "%.4f", $brevity_penalty * exp( $geometric_average ) ); 
}


sub get_sentence_bleu {
	my $self = shift;
	my $brevity_penalty = $self->_count_brevity_penalty();
	my $geometric_average = $self->_count_geometric_average( -10 );

	return sprintf( "%.4f", $brevity_penalty * exp( $geometric_average ) ); 
}


sub _count_brevity_penalty {
	my ( $self ) = @_;
	
	if( $self->machine_length() <= $self->reference_length() ) {
		return exp( 1 - $self->reference_length() / $self->machine_length() );
	} else {
		return 1;	
	}
}


sub _count_geometric_average {
	my ( $self, $ngram_not_found_income ) = @_;
	my @reference_ngrams = @{ $self->reference_ngrams() };
	my @common_ngrams = @{ $self->common_ngrams() }; 


	my $geometric_average = 0;
	for	my $length ( 1..4 ) {
		if ( ! exists $common_ngrams[ $length ]) {
			$geometric_average += $ngram_not_found_income;
		} else {
			my $common_ngrams_count = $self->_array_sum(
				values %{ $common_ngrams[ $length ] }
			);
			my $reference_ngrams_count = $self->_array_sum( 
				values %{ $reference_ngrams[ $length ] } 
			);
	
			my $ngram_precision = $common_ngrams_count / $reference_ngrams_count;
			$geometric_average += 1/4 * log( $ngram_precision ); 
		}
	}

	return $geometric_average;
}


sub _array_sum {
	my ( $self, @values ) = @_;
	
	my $sum = 0;
	for my $num ( @values ) {
		$sum += $num;
	}

	return $sum;
}


sub _add_reference_length {
	my ( $self, $reference ) = @_;
	
	$self->_reference_length(
		length( $reference ) + $self->reference_length()
	);
}


sub _add_machine_length {
	my( $self, $machine ) = @_;
	
	$self->_machine_length(
		length( $machine ) + $self->machine_length()
	);
}

sub _add_reference_ngrams {
	my ( $self, $reference_ngrams_ref ) = @_;

	$self->_reference_ngrams( 
		$self->_merge( $reference_ngrams_ref, $self->reference_ngrams() )
	);
}


sub _add_common_ngrams {
	my ( $self, $reference_ngrams_ref, $machine_ngrams_ref ) = @_;

	my @common_ngrams = $self->_find_common_ngrams(
		$reference_ngrams_ref,
		$machine_ngrams_ref
	);
	
	$self->_common_ngrams( 
		$self->_merge( \@common_ngrams, $self->common_ngrams() )
	);
}


sub _find_common_ngrams {
	my ( $self, $reference_ngrams_ref, $machine_ngrams_ref ) = @_;
	my @reference_ngrams = @{ $reference_ngrams_ref };
	my @machine_ngrams = @{ $machine_ngrams_ref };

	my @common_ngrams;
	for my $length ( 1..4 ) {		
		while ( 
			my ( $ngram, $count ) = each( %{ $reference_ngrams[ $length ] } ) 
		) {
			if ( exists $machine_ngrams[ $length ]{ $ngram } ) {
				$common_ngrams[ $length ]{ $ngram } = min(
					$count,
					$machine_ngrams[ $length ]{ $ngram }
				);
				
			}		
		}	
	}
	
	return @common_ngrams;
}


sub _add_redundant_ngrams {
	my ( $self, $reference_ngrams_ref, $machine_ngrams_ref ) = @_;

	my @redundant_ngrams = $self->_find_redundant_ngrams(
		$reference_ngrams_ref,
		$machine_ngrams_ref
	);
	
	$self->_redundant_ngrams( 
		$self->_merge( \@redundant_ngrams, $self->redundant_ngrams() )
	);
}


sub _find_redundant_ngrams {
	my ( $self, $reference_ngrams_ref, $machine_ngrams_ref ) = @_;
	return $self->_find_missing_ngrams( 
		$machine_ngrams_ref, 
		$reference_ngrams_ref 
	); 
}


sub _add_missing_ngrams {
	my ( $self, $reference_ngrams_ref, $machine_ngrams_ref ) = @_;

	my @missing_ngrams = $self->_find_missing_ngrams(
		$reference_ngrams_ref,
		$machine_ngrams_ref
	);
	
	$self->_missing_ngrams( 
		$self->_merge( \@missing_ngrams, $self->missing_ngrams() )
	);
}


sub _find_missing_ngrams {
	my ( $self, $reference_ngrams_ref, $machine_ngrams_ref ) = @_;
	my @reference_ngrams = @{ $reference_ngrams_ref };
	my @machine_ngrams = @{ $machine_ngrams_ref };


	my @missing_ngrams;
	for my $length ( 1..4 ) {		
		while ( 
			my ( $ngram, $count ) = each( %{ $reference_ngrams[ $length ] } ) 
		) {
			if( exists $machine_ngrams[ $length ]{ $ngram } ) {
				$count -= $machine_ngrams[ $length ]{ $ngram };
			}
						
			if ( $count > 0 ) {
				$missing_ngrams[ $length ]{ $ngram } = $count;			
			}		
		}	
	}
	
	return @missing_ngrams;
}


sub _count_ngrams {
	my $self = shift;
	my $sentence = shift;
	my @tokens = split( ' ', $sentence );
	
	my @ngrams;
	for my $length ( 1..4 ) {
		my @stack = @tokens[ 0..( $length-2 ) ];

		for my $token ( @tokens[ ( $length-1 )..$#tokens ] ) {
			push( @stack, $token );
			$ngrams[ $length ]{ join( ' ', @stack) }++;	
			shift( @stack );
		} 
	}
	
	return @ngrams;
}


sub _merge {
	my ( $self, $a_ref, $b_ref ) = @_;
	
	my @a;	
	if( $a_ref ) {
		@a = @{ $a_ref };
	}
	
	my @b; 
	if( $b_ref ) {
		@b = @{ $b_ref };
	}
		
	my @merged;
	for my $length ( 1..4 ) {
		while ( 
			my ( $ngram, $count ) = each( %{ $a[ $length ] } ) 
		) {	
			if( exists $b[ $length ]{ $ngram } ) {
				$count += $b[ $length ]{ $ngram };
			}
			
			$merged[ $length ]{ $ngram } = $count;		
		}
	
		while ( 
			my ( $ngram, $count ) = each( %{ $b[ $length ] } ) 
		) {	
			if( ! exists $a[ $length ]{ $ngram } ) {
				$merged[ $length ]{ $ngram } = $count;
			}
		}
	}	
	
	return \@merged;
}


sub print_ngrams {
	my $ngrams_ref = shift;
	my @ngrams = @{ $ngrams_ref };
	
	for my $length ( 1..4 ) {
		while ( my ( $ngram, $count ) = each( %{ $ngrams[ $length ] } ) ) {
			print "$ngram: $count\n";
		}
	}
}

1;
