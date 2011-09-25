#!/usr/bin/env perl

use warnings;
use strict;
use utf8;
use Data::Dumper;
use CGI;
use CGI::Carp qw ( fatalsToBrowser );
use Digest::MD5 qw( md5 );
use NGram;
use Text::WordDiff;
use Template;
use Text::Iconv;

my $QUERY = new CGI;
print $QUERY->header( -charset => 'utf-8' );

my $src = get_uploaded_file( 'src', 'Source text' );
my $ref = get_uploaded_file( 'ref', 'Reference translation' );
my $tst1 = get_uploaded_file( 'tst1', 'Machine translation 1' );
my $tst2 = get_uploaded_file( 'tst2', 'Machine translation 2' );

my %statistics = compute_statistics( $src, $ref, $tst1, $tst2 );
display_statistics( \%statistics );


sub get_uploaded_file {
	my ( $name, $label ) = @_;

	my $filename = $QUERY->param($name);
	if ( !$filename ) {
    	print 'There was a problem uploading ', $label, '.';
    	exit;
	}
	
	my $upload_filename = "/tmp/" . md5( $filename );
	my $upload_filehandle = $QUERY->upload($name);
	
	open ( UPLOADFILE, ">$upload_filename" ) or die "$!"; 
	binmode UPLOADFILE; 
	while ( <$upload_filehandle> ) {
		print UPLOADFILE;
	}
	close UPLOADFILE;

	return $upload_filename;
}

sub load_sentences {
	my( $filename ) = @_;
	open my $file, '<', $filename or die "Couldn't open $filename: $!";;
	my @sentences;

	while( my $sentence = <$file> ) {
		chomp $sentence ;
		push @sentences, $sentence ;
	}
	
	close $file;

	return @sentences;
}

sub compute_statistics {
	my ( $src, $ref, $tst1, $tst2 ) = @_;

	my @sentences_stats;
	my @src_sentences = load_sentences( $src );
	my @ref_sentences = load_sentences( $ref );
	my @tst1_sentences = load_sentences( $tst1 );
	my @tst2_sentences = load_sentences( $tst2 );

	my $tst1_ngrams = new NGram;
	my $tst2_ngrams = new NGram;
	while( $#src_sentences >= 0 ) {
		my $src_sentence = pop @src_sentences;
		my $ref_sentence = pop @ref_sentences;
		my $tst1_sentence = pop @tst1_sentences;
		my $tst2_sentence = pop @tst2_sentences;

		$tst1_ngrams->add_sentence( $ref_sentence, $tst1_sentence );
		$tst2_ngrams->add_sentence( $ref_sentence, $tst2_sentence );

		my %sentence = (
			'src' => $src_sentence,
			'ref' => $ref_sentence,
			'tst1_diff' => get_diff( $ref_sentence, $tst1_sentence ),
			'tst1_bleu' => get_bleu_for_sentence( $ref_sentence, $tst1_sentence ),
			'tst2_diff' => get_diff( $ref_sentence, $tst2_sentence ),
			'tst2_bleu' => get_bleu_for_sentence( $ref_sentence, $tst1_sentence ),
		);

		push @sentences_stats, \%sentence;
	}

	my %stats = (
		'tst1_bleu' => $tst1_ngrams->get_bleu(),
		'tst2_bleu' => $tst2_ngrams->get_bleu(),
		'sentences_stats' => \@sentences_stats,
	);

	return %stats;
}


sub get_bleu_for_sentence {
	my ( $ref, $tst ) = @_;

	my $ngram = new NGram;
	$ngram->add_sentence( $ref, $tst );

	return sprintf("%.4f", $ngram->get_bleu() );
}


sub get_diff {
	my ( $ref, $tst ) = @_;
	
	my $iconvUtfToIso = Text::Iconv->new( "utf8", "iso-8859-2" );
	my $iconvIsoToUtf =	Text::Iconv->new( "iso-8859-2", "utf8" );
		
	my $ref_iso = $iconvUtfToIso->convert( $ref );	
	my $tst_iso = $iconvUtfToIso->convert( $tst );
		
	my $diff = $iconvIsoToUtf->convert( word_diff( 
		\$tst_iso, \$ref_iso, 
		{ STYLE => 'HTML' } 
	) );

	return $diff;
}


sub display_statistics {
	my ( $statistics_ref ) = @_;
	%statistics = %{ $statistics_ref };

	my $template = Template->new();
    my $input = 'result.tt2';
    my $vars = {
    	'tst1_bleu' => $statistics{ 'tst1_bleu' },
    	'tst2_bleu' => $statistics{ 'tst2_bleu' },
    	'sentences' => $statistics{ 'sentences_stats' },
    };
    
    $template->process($input, $vars) or die $template->error();	
}



