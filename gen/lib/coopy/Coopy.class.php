<?php

class coopy_Coopy {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->extern_preference = false;
		$this->format_preference = null;
		$this->delim_preference = null;
		$this->output_format = "copy";
		$this->nested_output = false;
		$this->order_set = false;
		$this->order_preference = false;
	}}
	public $format_preference;
	public $delim_preference;
	public $extern_preference;
	public $output_format;
	public $nested_output;
	public $order_set;
	public $order_preference;
	public $io;
	public $mv;
	public function checkFormat($name) {
		if($this->extern_preference) {
			return $this->format_preference;
		}
		$ext = "";
		$pt = _hx_last_index_of($name, ".", null);
		if($pt >= 0) {
			$ext = strtolower(_hx_substr($name, $pt + 1, null));
			switch($ext) {
			case "json":{
				$this->format_preference = "json";
			}break;
			case "ndjson":{
				$this->format_preference = "ndjson";
			}break;
			case "csv":{
				$this->format_preference = "csv";
				$this->delim_preference = ",";
			}break;
			case "tsv":{
				$this->format_preference = "csv";
				$this->delim_preference = "\x09";
			}break;
			case "ssv":{
				$this->format_preference = "csv";
				$this->delim_preference = ";";
			}break;
			case "sqlite3":{
				$this->format_preference = "sqlite";
			}break;
			case "sqlite":{
				$this->format_preference = "sqlite";
			}break;
			default:{
				$ext = "";
			}break;
			}
		}
		$this->nested_output = $this->format_preference === "json" || $this->format_preference === "ndjson";
		$this->order_preference = !$this->nested_output;
		return $ext;
	}
	public function setFormat($name) {
		$this->extern_preference = false;
		$this->checkFormat("." . _hx_string_or_null($name));
		$this->extern_preference = true;
	}
	public function saveTable($name, $t) {
		if($this->output_format !== "copy") {
			$this->setFormat($this->output_format);
		}
		$txt = "";
		$this->checkFormat($name);
		if($this->format_preference === "csv") {
			$csv = new coopy_Csv($this->delim_preference);
			$txt = $csv->renderTable($t);
		} else {
			if($this->format_preference === "ndjson") {
				$txt = _hx_deref(new coopy_Ndjson($t))->render();
			} else {
				if($this->format_preference === "sqlite") {
					$this->io->writeStderr("! Cannot yet output to sqlite, aborting\x0A");
					return false;
				} else {
					$value = coopy_Coopy::jsonify($t);
					$txt = haxe_format_JsonPrinter::hprint($value, null, "  ");
				}
			}
		}
		return $this->saveText($name, $txt);
	}
	public function saveText($name, $txt) {
		if($name !== "-") {
			$this->io->saveContent($name, $txt);
		} else {
			$this->io->writeStdout($txt);
		}
		return true;
	}
	public function loadTable($name) {
		$txt = $this->io->getContent($name);
		$ext = $this->checkFormat($name);
		if($ext === "sqlite") {
			$sql = $this->io->openSqliteDatabase($name);
			if($sql === null) {
				$this->io->writeStderr("! Cannot open database, aborting\x0A");
				return null;
			}
			$helper = new coopy_SqliteHelper();
			$names = $helper->getTableNames($sql);
			if($names === null) {
				$this->io->writeStderr("! Cannot find database tables, aborting\x0A");
				return null;
			}
			if($names->length === 0) {
				$this->io->writeStderr("! No tables in database, aborting\x0A");
				return null;
			}
			$tab = new coopy_SqlTable($sql, new coopy_SqlTableName($names[0], null), $helper);
			return $tab;
		}
		if($ext === "ndjson") {
			$t = new coopy_SimpleTable(0, 0);
			$ndjson = new coopy_Ndjson($t);
			$ndjson->parse($txt);
			return $t;
		}
		if($ext === "json" || $ext === "") {
			try {
				$json = _hx_deref(new haxe_format_JsonParser($txt))->parseRec();
				$this->format_preference = "json";
				$t1 = coopy_Coopy::jsonToTable($json);
				if($t1 === null) {
					throw new HException("JSON failed");
				}
				return $t1;
			}catch(Exception $__hx__e) {
				$_ex_ = ($__hx__e instanceof HException) ? $__hx__e->e : $__hx__e;
				$e = $_ex_;
				{
					if($ext === "json") {
						throw new HException($e);
					}
				}
			}
		}
		$this->format_preference = "csv";
		$csv = new coopy_Csv($this->delim_preference);
		$output = new coopy_SimpleTable(0, 0);
		$csv->parseTable($txt, $output);
		if($output !== null) {
			$output->trimBlank();
		}
		return $output;
	}
	public $status;
	public $daff_cmd;
	public function command($io, $cmd, $args) {
		$r = 0;
		if($io->async()) {
			$r = $io->command($cmd, $args);
		}
		if($r !== 999) {
			$io->writeStdout("\$ " . _hx_string_or_null($cmd));
			{
				$_g = 0;
				while($_g < $args->length) {
					$arg = $args[$_g];
					++$_g;
					$io->writeStdout(" ");
					$spaced = _hx_index_of($arg, " ", null) >= 0;
					if($spaced) {
						$io->writeStdout("\"");
					}
					$io->writeStdout($arg);
					if($spaced) {
						$io->writeStdout("\"");
					}
					unset($spaced,$arg);
				}
			}
			$io->writeStdout("\x0A");
		}
		if(!$io->async()) {
			$r = $io->command($cmd, $args);
		}
		return $r;
	}
	public function installGitDriver($io, $formats) {
		$r = 0;
		if($this->status === null) {
			$this->status = new haxe_ds_StringMap();
			$this->daff_cmd = "";
		}
		$key = "hello";
		if(!$this->status->exists($key)) {
			$io->writeStdout("Setting up git to use daff on");
			{
				$_g = 0;
				while($_g < $formats->length) {
					$format = $formats[$_g];
					++$_g;
					$io->writeStdout(" *." . _hx_string_or_null($format));
					unset($format);
				}
			}
			$io->writeStdout(" files\x0A");
			$this->status->set($key, $r);
		}
		$key = "can_run_git";
		if(!$this->status->exists($key)) {
			$r = $this->command($io, "git", (new _hx_array(array("--version"))));
			if($r === 999) {
				return $r;
			}
			$this->status->set($key, $r);
			if($r !== 0) {
				$io->writeStderr("! Cannot run git, aborting\x0A");
				return 1;
			}
			$io->writeStdout("- Can run git\x0A");
		}
		$daffs = (new _hx_array(array("daff", "daff.rb", "daff.py")));
		if($this->daff_cmd === "") {
			{
				$_g1 = 0;
				while($_g1 < $daffs->length) {
					$daff = $daffs[$_g1];
					++$_g1;
					$key1 = "can_run_" . _hx_string_or_null($daff);
					if(!$this->status->exists($key1)) {
						$r = $this->command($io, $daff, (new _hx_array(array("version"))));
						if($r === 999) {
							return $r;
						}
						$this->status->set($key1, $r);
						if($r === 0) {
							$this->daff_cmd = $daff;
							$io->writeStdout("- Can run " . _hx_string_or_null($daff) . " as \"" . _hx_string_or_null($daff) . "\"\x0A");
							break;
						}
					}
					unset($key1,$daff);
				}
			}
			if($this->daff_cmd === "") {
				$io->writeStderr("! Cannot find daff, is it in your path?\x0A");
				return 1;
			}
		}
		{
			$_g2 = 0;
			while($_g2 < $formats->length) {
				$format1 = $formats[$_g2];
				++$_g2;
				$key = "have_diff_driver_" . _hx_string_or_null($format1);
				if(!$this->status->exists($key)) {
					$r = $this->command($io, "git", (new _hx_array(array("config", "--global", "--get", "diff.daff-" . _hx_string_or_null($format1) . ".command"))));
					if($r === 999) {
						return $r;
					}
					$this->status->set($key, $r);
				}
				$have_diff_driver = $this->status->get($key) === 0;
				$key = "add_diff_driver_" . _hx_string_or_null($format1);
				if(!$this->status->exists($key)) {
					if(!$have_diff_driver) {
						$r = $this->command($io, "git", (new _hx_array(array("config", "--global", "diff.daff-" . _hx_string_or_null($format1) . ".command", _hx_string_or_null($this->daff_cmd) . " diff --color --git"))));
						if($r === 999) {
							return $r;
						}
						$io->writeStdout("- Added diff driver for " . _hx_string_or_null($format1) . "\x0A");
					} else {
						$r = 0;
						$io->writeStdout("- Already have diff driver for " . _hx_string_or_null($format1) . ", not touching it\x0A");
					}
					$this->status->set($key, $r);
				}
				$key = "have_merge_driver_" . _hx_string_or_null($format1);
				if(!$this->status->exists($key)) {
					$r = $this->command($io, "git", (new _hx_array(array("config", "--global", "--get", "merge.daff-" . _hx_string_or_null($format1) . ".driver"))));
					if($r === 999) {
						return $r;
					}
					$this->status->set($key, $r);
				}
				$have_merge_driver = $this->status->get($key) === 0;
				$key = "name_merge_driver_" . _hx_string_or_null($format1);
				if(!$this->status->exists($key)) {
					if(!$have_merge_driver) {
						$r = $this->command($io, "git", (new _hx_array(array("config", "--global", "merge.daff-" . _hx_string_or_null($format1) . ".name", "daff tabular " . _hx_string_or_null($format1) . " merge"))));
						if($r === 999) {
							return $r;
						}
					} else {
						$r = 0;
					}
					$this->status->set($key, $r);
				}
				$key = "add_merge_driver_" . _hx_string_or_null($format1);
				if(!$this->status->exists($key)) {
					if(!$have_merge_driver) {
						$r = $this->command($io, "git", (new _hx_array(array("config", "--global", "merge.daff-" . _hx_string_or_null($format1) . ".driver", _hx_string_or_null($this->daff_cmd) . " merge --output %A %O %A %B"))));
						if($r === 999) {
							return $r;
						}
						$io->writeStdout("- Added merge driver for " . _hx_string_or_null($format1) . "\x0A");
					} else {
						$r = 0;
						$io->writeStdout("- Already have merge driver for " . _hx_string_or_null($format1) . ", not touching it\x0A");
					}
					$this->status->set($key, $r);
				}
				unset($have_merge_driver,$have_diff_driver,$format1);
			}
		}
		if(!$io->exists(".git/config")) {
			$io->writeStderr("! This next part needs to happen in a git repository.\x0A");
			$io->writeStderr("! Please run again from the root of a git repository.\x0A");
			return 1;
		}
		$attr = ".gitattributes";
		$txt = "";
		$post = "";
		if(!$io->exists($attr)) {
			$io->writeStdout("- No .gitattributes file\x0A");
		} else {
			$io->writeStdout("- You have a .gitattributes file\x0A");
			$txt = $io->getContent($attr);
		}
		$need_update = false;
		{
			$_g3 = 0;
			while($_g3 < $formats->length) {
				$format2 = $formats[$_g3];
				++$_g3;
				if(_hx_index_of($txt, "*." . _hx_string_or_null($format2), null) >= 0) {
					$io->writeStderr("- Your .gitattributes file already mentions *." . _hx_string_or_null($format2) . "\x0A");
				} else {
					$post .= "*." . _hx_string_or_null($format2) . " diff=daff-" . _hx_string_or_null($format2) . "\x0A";
					$post .= "*." . _hx_string_or_null($format2) . " merge=daff-" . _hx_string_or_null($format2) . "\x0A";
					$io->writeStdout("- Placing the following lines in .gitattributes:\x0A");
					$io->writeStdout($post);
					if($txt !== "" && !$need_update) {
						$txt .= "\x0A";
					}
					$txt .= _hx_string_or_null($post);
					$need_update = true;
				}
				unset($format2);
			}
		}
		if($need_update) {
			$io->saveContent($attr, $txt);
		}
		$io->writeStdout("- Done!\x0A");
		return 0;
	}
	public function coopyhx($io) {
		$args = $io->args();
		if($args[0] === "--keep") {
			return coopy_Coopy::keepAround();
		}
		$more = true;
		$output = null;
		$css_output = null;
		$fragment = false;
		$pretty = true;
		$inplace = false;
		$git = false;
		$color = false;
		$flags = new coopy_CompareFlags();
		$flags->always_show_header = true;
		while($more) {
			$more = false;
			{
				$_g1 = 0;
				$_g = $args->length;
				while($_g1 < $_g) {
					$i = $_g1++;
					$tag = $args[$i];
					if($tag === "--output") {
						$more = true;
						$output = $args[$i + 1];
						$args->splice($i, 2);
						break;
					} else {
						if($tag === "--css") {
							$more = true;
							$fragment = true;
							$css_output = $args[$i + 1];
							$args->splice($i, 2);
							break;
						} else {
							if($tag === "--fragment") {
								$more = true;
								$fragment = true;
								$args->splice($i, 1);
								break;
							} else {
								if($tag === "--plain") {
									$more = true;
									$pretty = false;
									$args->splice($i, 1);
									break;
								} else {
									if($tag === "--all") {
										$more = true;
										$flags->show_unchanged = true;
										$args->splice($i, 1);
										break;
									} else {
										if($tag === "--act") {
											$more = true;
											if($flags->acts === null) {
												$flags->acts = new haxe_ds_StringMap();
											}
											{
												$flags->acts->set($args[$i + 1], true);
												true;
											}
											$args->splice($i, 2);
											break;
										} else {
											if($tag === "--context") {
												$more = true;
												$context = Std::parseInt($args[$i + 1]);
												if($context >= 0) {
													$flags->unchanged_context = $context;
												}
												$args->splice($i, 2);
												break;
												unset($context);
											} else {
												if($tag === "--inplace") {
													$more = true;
													$inplace = true;
													$args->splice($i, 1);
													break;
												} else {
													if($tag === "--git") {
														$more = true;
														$git = true;
														$args->splice($i, 1);
														break;
													} else {
														if($tag === "--unordered") {
															$more = true;
															$flags->ordered = false;
															$flags->unchanged_context = 0;
															$this->order_set = true;
															$args->splice($i, 1);
															break;
														} else {
															if($tag === "--ordered") {
																$more = true;
																$flags->ordered = true;
																$this->order_set = true;
																$args->splice($i, 1);
																break;
															} else {
																if($tag === "--color") {
																	$more = true;
																	$color = true;
																	$args->splice($i, 1);
																	break;
																} else {
																	if($tag === "--input-format") {
																		$more = true;
																		$this->setFormat($args[$i + 1]);
																		$args->splice($i, 2);
																		break;
																	} else {
																		if($tag === "--output-format") {
																			$more = true;
																			$this->output_format = $args[$i + 1];
																			$args->splice($i, 2);
																			break;
																		} else {
																			if($tag === "--id") {
																				$more = true;
																				if($flags->ids === null) {
																					$flags->ids = new _hx_array(array());
																				}
																				$flags->ids->push($args[$i + 1]);
																				$args->splice($i, 2);
																				break;
																			} else {
																				if($tag === "--ignore") {
																					$more = true;
																					if($flags->columns_to_ignore === null) {
																						$flags->columns_to_ignore = new _hx_array(array());
																					}
																					$flags->columns_to_ignore->push($args[$i + 1]);
																					$args->splice($i, 2);
																					break;
																				} else {
																					if($tag === "--index") {
																						$more = true;
																						$flags->always_show_order = true;
																						$flags->never_show_order = false;
																						$args->splice($i, 1);
																						break;
																					}
																				}
																			}
																		}
																	}
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
					unset($tag,$i);
				}
				unset($_g1,$_g);
			}
		}
		$cmd = $args[0];
		if($args->length < 2) {
			if($cmd === "version") {
				$io->writeStdout(_hx_string_or_null(coopy_Coopy::$VERSION) . "\x0A");
				return 0;
			}
			if($cmd === "git") {
				$io->writeStdout("You can use daff to improve git's handling of csv files, by using it as a\x0Adiff driver (for showing what has changed) and as a merge driver (for merging\x0Achanges between multiple versions).\x0A");
				$io->writeStdout("\x0A");
				$io->writeStdout("Automatic setup\x0A");
				$io->writeStdout("---------------\x0A\x0A");
				$io->writeStdout("Run:\x0A");
				$io->writeStdout("  daff git csv\x0A");
				$io->writeStdout("\x0A");
				$io->writeStdout("Manual setup\x0A");
				$io->writeStdout("------------\x0A\x0A");
				$io->writeStdout("Create and add a file called .gitattributes in the root directory of your\x0Arepository, containing:\x0A\x0A");
				$io->writeStdout("  *.csv diff=daff-csv\x0A");
				$io->writeStdout("  *.csv merge=daff-csv\x0A");
				$io->writeStdout("\x0ACreate a file called .gitconfig in your home directory (or alternatively\x0Aopen .git/config for a particular repository) and add:\x0A\x0A");
				$io->writeStdout("  [diff \"daff-csv\"]\x0A");
				$io->writeStdout("  command = daff diff --color --git\x0A");
				$io->writeStderr("\x0A");
				$io->writeStdout("  [merge \"daff-csv\"]\x0A");
				$io->writeStdout("  name = daff tabular merge\x0A");
				$io->writeStdout("  driver = daff merge --output %A %O %A %B\x0A\x0A");
				$io->writeStderr("Make sure you can run daff from the command-line as just \"daff\" - if not,\x0Areplace \"daff\" in the driver and command lines above with the correct way\x0Ato call it. Omit --color if your terminal does not support ANSI colors.");
				$io->writeStderr("\x0A");
				return 0;
			}
			$io->writeStderr("daff can produce and apply tabular diffs.\x0A");
			$io->writeStderr("Call as:\x0A");
			$io->writeStderr("  daff [--color] [--output OUTPUT.csv] a.csv b.csv\x0A");
			$io->writeStderr("  daff [--output OUTPUT.csv] parent.csv a.csv b.csv\x0A");
			$io->writeStderr("  daff [--output OUTPUT.ndjson] a.ndjson b.ndjson\x0A");
			$io->writeStderr("  daff patch [--inplace] [--output OUTPUT.csv] a.csv patch.csv\x0A");
			$io->writeStderr("  daff merge [--inplace] [--output OUTPUT.csv] parent.csv a.csv b.csv\x0A");
			$io->writeStderr("  daff trim [--output OUTPUT.csv] source.csv\x0A");
			$io->writeStderr("  daff render [--output OUTPUT.html] diff.csv\x0A");
			$io->writeStderr("  daff copy in.csv out.tsv\x0A");
			$io->writeStderr("  daff git\x0A");
			$io->writeStderr("  daff version\x0A");
			$io->writeStderr("\x0A");
			$io->writeStderr("The --inplace option to patch and merge will result in modification of a.csv.\x0A");
			$io->writeStderr("\x0A");
			$io->writeStderr("If you need more control, here is the full list of flags:\x0A");
			$io->writeStderr("  daff diff [--output OUTPUT.csv] [--context NUM] [--all] [--act ACT] a.csv b.csv\x0A");
			$io->writeStderr("     --act ACT:     show only a certain kind of change (update, insert, delete)\x0A");
			$io->writeStderr("     --all:         do not prune unchanged rows\x0A");
			$io->writeStderr("     --color:       highlight changes with terminal colors\x0A");
			$io->writeStderr("     --context NUM: show NUM rows of context\x0A");
			$io->writeStderr("     --id:          specify column to use as primary key (repeat for multi-column key)\x0A");
			$io->writeStderr("     --ignore:      specify column to ignore completely (can repeat)\x0A");
			$io->writeStderr("     --input-format [csv|tsv|ssv|json]: set format to expect for input\x0A");
			$io->writeStderr("     --ordered:     assume row order is meaningful (default for CSV)\x0A");
			$io->writeStderr("     --output-format [csv|tsv|ssv|json|copy]: set format for output\x0A");
			$io->writeStderr("     --unordered:   assume row order is meaningless (default for json formats)\x0A");
			$io->writeStderr("\x0A");
			$io->writeStderr("  daff diff --git path old-file old-hex old-mode new-file new-hex new-mode\x0A");
			$io->writeStderr("     --git:         process arguments provided by git to diff drivers\x0A");
			$io->writeStderr("     --index:       include row/columns numbers from orginal tables\x0A");
			$io->writeStderr("\x0A");
			$io->writeStderr("  daff render [--output OUTPUT.html] [--css CSS.css] [--fragment] [--plain] diff.csv\x0A");
			$io->writeStderr("     --css CSS.css: generate a suitable css file to go with the html\x0A");
			$io->writeStderr("     --fragment:    generate just a html fragment rather than a page\x0A");
			$io->writeStderr("     --plain:       do not use fancy utf8 characters to make arrows prettier\x0A");
			return 1;
		}
		$cmd1 = $args[0];
		$offset = 1;
		if(!Lambda::has((new _hx_array(array("diff", "patch", "merge", "trim", "render", "git", "version", "copy"))), $cmd1)) {
			if(_hx_index_of($cmd1, ".", null) !== -1 || _hx_index_of($cmd1, "--", null) === 0) {
				$cmd1 = "diff";
				$offset = 0;
			}
		}
		if($cmd1 === "git") {
			$types = $args->splice($offset, $args->length - $offset);
			return $this->installGitDriver($io, $types);
		}
		if($git) {
			$ct = $args->length - $offset;
			if($ct !== 7) {
				$io->writeStderr("Expected 7 parameters from git, but got " . _hx_string_rec($ct, "") . "\x0A");
				return 1;
			}
			$git_args = $args->splice($offset, $ct);
			$args->splice(0, $args->length);
			$offset = 0;
			$path = $git_args[0];
			$old_file = $git_args[1];
			$new_file = $git_args[4];
			$io->writeStdout("--- a/" . _hx_string_or_null($path) . "\x0A");
			$io->writeStdout("+++ b/" . _hx_string_or_null($path) . "\x0A");
			$args->push($old_file);
			$args->push($new_file);
		}
		$tool = $this;
		$tool->io = $io;
		$parent = null;
		if($args->length - $offset >= 3) {
			$parent = $tool->loadTable($args[$offset]);
			$offset++;
		}
		$aname = $args[$offset];
		$a = $tool->loadTable($aname);
		$b = null;
		if($args->length - $offset >= 2) {
			if($cmd1 !== "copy") {
				$b = $tool->loadTable($args[1 + $offset]);
			} else {
				$output = $args[1 + $offset];
			}
		}
		if($inplace) {
			if($output !== null) {
				$io->writeStderr("Please do not use --inplace when specifying an output.\x0A");
			}
			$output = $aname;
			return 1;
		}
		if($output === null) {
			$output = "-";
		}
		$ok = true;
		if($cmd1 === "diff") {
			if(!$this->order_set) {
				$flags->ordered = $this->order_preference;
				if(!$flags->ordered) {
					$flags->unchanged_context = 0;
				}
			}
			$flags->allow_nested_cells = $this->nested_output;
			$ct1 = coopy_Coopy::compareTables3($parent, $a, $b, $flags);
			$align = $ct1->align();
			$td = new coopy_TableDiff($align, $flags);
			$o = new coopy_SimpleTable(0, 0);
			$td->hilite($o);
			if($color) {
				$render = new coopy_TerminalDiffRender();
				$tool->saveText($output, $render->render($o));
			} else {
				$tool->saveTable($output, $o);
			}
		} else {
			if($cmd1 === "patch") {
				$patcher = new coopy_HighlightPatch($a, $b);
				$patcher->apply();
				$tool->saveTable($output, $a);
			} else {
				if($cmd1 === "merge") {
					$merger = new coopy_Merger($parent, $a, $b, $flags);
					$conflicts = $merger->apply();
					$ok = $conflicts === 0;
					if($conflicts > 0) {
						$io->writeStderr(_hx_string_rec($conflicts, "") . " conflict" . _hx_string_or_null(((($conflicts > 1) ? "s" : ""))) . "\x0A");
					}
					$tool->saveTable($output, $a);
				} else {
					if($cmd1 === "trim") {
						$tool->saveTable($output, $a);
					} else {
						if($cmd1 === "render") {
							$renderer = new coopy_DiffRender();
							$renderer->usePrettyArrows($pretty);
							$renderer->render($a);
							if(!$fragment) {
								$renderer->completeHtml();
							}
							$tool->saveText($output, $renderer->html());
							if($css_output !== null) {
								$tool->saveText($css_output, $renderer->sampleCss());
							}
						} else {
							if($cmd1 === "copy") {
								$tool->saveTable($output, $a);
							}
						}
					}
				}
			}
		}
		if($ok) {
			return 0;
		} else {
			return 1;
		}
	}
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->__dynamics[$m]) && is_callable($this->__dynamics[$m]))
			return call_user_func_array($this->__dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call <'.$m.'>');
	}
	static $VERSION = "1.2.6";
	static function compareTables($local, $remote, $flags = null) {
		$comp = new coopy_TableComparisonState();
		$comp->a = $local;
		$comp->b = $remote;
		$comp->compare_flags = $flags;
		$ct = new coopy_CompareTable($comp);
		return $ct;
	}
	static function compareTables3($parent, $local, $remote, $flags = null) {
		$comp = new coopy_TableComparisonState();
		$comp->p = $parent;
		$comp->a = $local;
		$comp->b = $remote;
		$comp->compare_flags = $flags;
		$ct = new coopy_CompareTable($comp);
		return $ct;
	}
	static function keepAround() {
		$st = new coopy_SimpleTable(1, 1);
		$v = new coopy_Viterbi();
		$td = new coopy_TableDiff(null, null);
		$idx = new coopy_Index();
		$dr = new coopy_DiffRender();
		$cf = new coopy_CompareFlags();
		$hp = new coopy_HighlightPatch(null, null);
		$csv = new coopy_Csv(null);
		$tm = new coopy_TableModifier(null);
		$sc = new coopy_SqlCompare(null, null, null);
		return 0;
	}
	static function cellFor($x) {
		return $x;
	}
	static function jsonToTable($json) {
		$output = null;
		{
			$_g = 0;
			$_g1 = Reflect::fields($json);
			while($_g < $_g1->length) {
				$name = $_g1[$_g];
				++$_g;
				$t = Reflect::field($json, $name);
				$columns = Reflect::field($t, "columns");
				if($columns === null) {
					continue;
				}
				$rows = Reflect::field($t, "rows");
				if($rows === null) {
					continue;
				}
				$output = new coopy_SimpleTable($columns->length, $rows->length);
				$has_hash = false;
				$has_hash_known = false;
				{
					$_g3 = 0;
					$_g2 = $rows->length;
					while($_g3 < $_g2) {
						$i = $_g3++;
						$row = $rows[$i];
						if(!$has_hash_known) {
							if(Reflect::fields($row)->length === $columns->length) {
								$has_hash = true;
							}
							$has_hash_known = true;
						}
						if(!$has_hash) {
							$lst = $row;
							{
								$_g5 = 0;
								$_g4 = $columns->length;
								while($_g5 < $_g4) {
									$j = $_g5++;
									$val = $lst[$j];
									$output->setCell($j, $i, coopy_Coopy::cellFor($val));
									unset($val,$j);
								}
								unset($_g5,$_g4);
							}
							unset($lst);
						} else {
							$_g51 = 0;
							$_g41 = $columns->length;
							while($_g51 < $_g41) {
								$j1 = $_g51++;
								$val1 = Reflect::field($row, $columns[$j1]);
								$output->setCell($j1, $i, coopy_Coopy::cellFor($val1));
								unset($val1,$j1);
							}
							unset($_g51,$_g41);
						}
						unset($row,$i);
					}
					unset($_g3,$_g2);
				}
				unset($t,$rows,$name,$has_hash_known,$has_hash,$columns);
			}
		}
		if($output !== null) {
			$output->trimBlank();
		}
		return $output;
	}
	static function main() {
		$io = new coopy_TableIO();
		$coopy1 = new coopy_Coopy();
		return $coopy1->coopyhx($io);
	}
	static function show($t) {
		$w = $t->get_width();
		$h = $t->get_height();
		$txt = "";
		{
			$_g = 0;
			while($_g < $h) {
				$y = $_g++;
				{
					$_g1 = 0;
					while($_g1 < $w) {
						$x = $_g1++;
						$txt .= Std::string($t->getCell($x, $y));
						$txt .= " ";
						unset($x);
					}
					unset($_g1);
				}
				$txt .= "\x0A";
				unset($y);
			}
		}
		haxe_Log::trace($txt, _hx_anonymous(array("fileName" => "Coopy.hx", "lineNumber" => 794, "className" => "coopy.Coopy", "methodName" => "show")));
	}
	static function jsonify($t) {
		$workbook = new haxe_ds_StringMap();
		$sheet = new _hx_array(array());
		$w = $t->get_width();
		$h = $t->get_height();
		$txt = "";
		{
			$_g = 0;
			while($_g < $h) {
				$y = $_g++;
				$row = new _hx_array(array());
				{
					$_g1 = 0;
					while($_g1 < $w) {
						$x = $_g1++;
						$v = $t->getCell($x, $y);
						$row->push($v);
						unset($x,$v);
					}
					unset($_g1);
				}
				$sheet->push($row);
				unset($y,$row);
			}
		}
		$workbook->set("sheet", $sheet);
		return $workbook;
	}
	function __toString() { return 'coopy.Coopy'; }
}
